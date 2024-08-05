<?php

namespace App\Jobs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Helper;
use VideoThumbnail;
use App\Models\CollectionFile;
use App\Jobs\GenerateThumbnail;

class GenerateThumbnail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $thumbnails;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($thumbnails)
    {
        $this->thumbnails = $thumbnails;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach($this->thumbnails as $thumbnail){
            $collection_file = CollectionFile::firstWhere('unique_id', $thumbnail['unique_id'] ?? 0);
            if(isset($thumbnail['file_url']) && $collection_file){
                info("Thumbnail generation start " . $thumbnail['file_url'] ." | " . $thumbnail['unique_id']);
                $thumbnail_directory = 'public/uploads/thumbnails';
                $thumbnail_path = storage_path('app/' . $thumbnail_directory);
                if (!Storage::exists($thumbnail_directory)) {
                    Storage::makeDirectory($thumbnail_directory);
                }
                $file_name = Helper::file_name().".jpg";
                \VideoThumbnail::createThumbnail("http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4", $thumbnail_path, $file_name, 5, 1920, 1080);
                $thumbanil_storage_path = "$thumbnail_directory/$file_name";
                if(Storage::exists($thumbanil_storage_path)){
                   $collection_file->update([
                    'preview_file' => asset(Storage::url($thumbanil_storage_path))
                   ]);
                    info("file generated $collection_file->preview_file");
                }else{
                    info("file not generated $thumbanil_storage_path");
                }
                info("Thumbnail generation end " . $thumbnail['file_url'] ." | " . $thumbnail['unique_id']);
            }else{
              info("file url or collection file not found");
            }
        }
    }
}