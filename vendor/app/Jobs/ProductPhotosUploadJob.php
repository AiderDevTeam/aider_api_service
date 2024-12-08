namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;

class ProductPhotosUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $product;
    protected $photoUrls;

    /**
     * Create a new job instance.
     *
     * @param Product $product
     * @param array $photoUrls
     */
    public function __construct(Product $product, array $photoUrls)
    {
        $this->product = $product;
        $this->photoUrls = $photoUrls;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->photoUrls as $photoUrl) {
            $this->product->photos()->create(['photo_url' => $photoUrl]);
        }
    }
}
