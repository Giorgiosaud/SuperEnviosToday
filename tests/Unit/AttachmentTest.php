<?php

    namespace Tests\Unit;

    use App\Attachment;
    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Tests\TestCase;
    use Illuminate\Http\UploadedFile;
    use Illuminate\Support\Facades\Storage;

    class AttachmentTest extends TestCase
    {
        use RefreshDatabase;

        public function addAttachment()
        {
            Storage::fake('public');
            $this->postJson('api/attachment', [
                'file' => UploadedFile::fake()->image('avatar2.jpg')
            ]);
            $attachment = Attachment::where('name', 'LIKE', '%avatar%')->get();
            $this->assertCount(1, $attachment);
            $path = str_replace('storage', '',$attachment->first()->path);
            Storage::disk('public')->assertExists($path);

        }
        public function tryToAddAttachment(){
            Storage::fake('public');
            $allAttachments=Attachment::all();
            $this->postJson('api/attachment', [
                'file' => UploadedFile::fake()->image('avatar2.jpg')
            ]);
            $allAttachmentsPost=Attachment::all();
            $this->assertEquals($allAttachmentsPost,$allAttachments);
            $this->assertCount(1,$allAttachmentsPost);
        }

        /**
         * @test
         **/
        public function addAttachmentAsCoordinator()
        {
            $this->actingAsCoordinator();
            $this->addAttachment();

        }

        /**
         * @test
         **/
        public function addAttachmentAsForeignOperator()
        {
            $this->actingAsForeignOperator()->roles;
            $this->addAttachment();
        }

        /**
         * @test
         **/
        public function addAttachmentAsVenezuelanOperator()
        {
            $this->actingAsVenezuelanOperator();
            $this->addAttachment();
        }

        /**
         * @test
         **/
        public function addAttachmentAsClient()
        {
            $this->actingAsClient();
            $this->addAttachment();
        }

        /**
         * @test
         **/
        public function addAttachmentAsReceiver()
        {
            $this->actingAsReceiver();
            $this->addAttachment();
        }

        /**
         * @test
         **/
        public function addAttachmentAsGuess()
        {
            $this->addAttachment();
        }

    }
