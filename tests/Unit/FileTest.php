<?php

    namespace Tests\Unit;

    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Tests\TestCase;

    class FileTest extends TestCase
    {
        use RefreshDatabase;

        public function addAttachmentAsCoordinator()
        {
        $this->actingAsCoordinator();
        }

        public function addAttachmentAsForeignOperator()
        {
        }

        public function addAttachmentAsVenezuelanOperator()
        {
        }
    }
