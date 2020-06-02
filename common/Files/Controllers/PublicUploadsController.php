<?php namespace Common\Files\Controllers;

use Common\Core\BaseController;
use Common\Files\Actions\UploadFile;
use Common\Files\FileEntry;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PublicUploadsController extends BaseController {

    /**
     * @var Request
     */
    private $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return JsonResponse
     */
    public function videos()
    {
        $this->authorize('store', FileEntry::class);

        $this->validate($this->request, [
            'diskPrefix' => 'required|string|min:1',
            'file' => 'required|file'
        ]);

        $fileEntry = app(UploadFile::class)
            ->execute('public', $this->request->file('file'), $this->request->except('file'));

        return response(['fileEntry' => $fileEntry], 201);
    }

    /**
     * @return ResponseFactory|Response
     */
    public function images() {

        $this->authorize('store', FileEntry::class);

        $this->validate($this->request, [
            'diskPrefix' => 'required|string|min:1',
            'file' => 'required|file'
        ]);

        $fileEntry = app(UploadFile::class)
            ->execute('public', $this->request->file('file'), $this->request->except('file'));

        return response(['fileEntry' => $fileEntry], 201);
    }
}
