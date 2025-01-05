<?php

namespace App\Console\Commands;

use App\Models\RatingPictures;
use App\Models\ReportCharacteristic;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UploadPicturesToS3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ratingpictureupload:s3';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            DB::beginTransaction();
            $ratingPictures = RatingPictures::select('name')->distinct()->whereNotNull('name')->get();
            $reportCharacteristics = ReportCharacteristic::with('emoji')->get();
            $localPath = "assets/Frontend/images/reportemoji/";
            $s3path = config('constants.mediaAssets.ratingPicture.folderName');
            foreach ($ratingPictures as $rp) {
                Storage::disk('s3')->putFileAs($s3path, Storage::disk('public_asset')->path($localPath . '' . $rp->name), $rp->name);
            }
            DB::statement('update report_characteristics set rating_picture_id = null;');
            Schema::disableForeignKeyConstraints();
            RatingPictures::truncate();
            Schema::enableForeignKeyConstraints();
            if (Schema::hasColumn('rating_pictures', 'report_characteristic_id')) {
                Schema::table('rating_pictures', function (Blueprint $table) {

                    $table->dropConstrainedForeignId('report_characteristic_id');
                });
            }
            foreach ($ratingPictures as $ratingPicture) {
                RatingPictures::create([
                    'name' => $ratingPicture->name,
                ]);
            }
            $newRatingPictures = RatingPictures::all();
            foreach ($reportCharacteristics as $rp) {
                if ($rp->emoji) {
                    $ratingPic = RatingPictures::where('name', $rp->emoji->name)->first();
                    if ($ratingPic) {
                        $rp->rating_picture_id = RatingPictures::where('name', $rp->emoji->name)->first()->id;
                        $rp->save();
                    }
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            print($e->getMessage());
        }
        return 0;
    }
}
