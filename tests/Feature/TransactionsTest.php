<?php

    namespace Tests\Feature;

    use Tests\TestCase;
    use Illuminate\Foundation\Testing\RefreshDatabase;

    /**
     * Class UserTest
     * @package Tests\Feature
     */
    class TransactionsTest extends TestCase
    {
        use RefreshDatabase;


        /**
         * A basic test example.
         * @test
         * TODO
         * @return void
         */
        public function actingAsAnyLoggedInCanSeeBanks()
        {
            $this->assertEquals(3, 3);

        }
    }

