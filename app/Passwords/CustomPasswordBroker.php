<?php

namespace App\Passwords;

use Illuminate\Auth\Passwords\PasswordBroker;
  use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
  use Illuminate\Support\Arr;
  use UnexpectedValueException;

  class CustomPasswordBroker extends PasswordBroker
  {
      protected $users = CustomUserProvider::class;

      /**
       * Validate a password reset for the given credentials.
       *
       * @param array $credentials
       *
       * @return \Illuminate\Contracts\Auth\CanResetPassword|string
       */
      protected function validateReset(array $credentials)
      {
          if (is_null($user = $this->getUser($credentials))) {
              return static::INVALID_USER;
          }
          if (!$this->validateNewPassword($credentials)) {
              return static::INVALID_PASSWORD;
          }

          if (!$this->tokens->exists($user, $credentials['token'])) {
              return static::INVALID_TOKEN;
          }

          return $user;
      }

      /**
       * Get the user for the given credentials.
       *
       * @param array $credentials
       *
       * @throws \UnexpectedValueException
       *
       * @return \Illuminate\Contracts\Auth\CanResetPassword|null
       */
      public function getUser(array $credentials)
      {
          $credentials = Arr::except($credentials, ['token']);
          $user = $this->users->retrieveByCredentials($credentials);
          if ($user && !$user instanceof CanResetPasswordContract) {
              throw new UnexpectedValueException('User must implement CanResetPassword interface.');
          }

          return $user;
      }
  }
