<?php

namespace App\Http\Services;

use Carbon\Carbon;
use Google\Cloud\Core\Exception\GoogleException;
use Google\Cloud\Firestore\CollectionReference;
use Google\Cloud\Firestore\FirestoreClient;

class FirestoreService
{
    private FirestoreClient $firestore;

    /**
     * @throws GoogleException
     */
    public function __construct()
    {
        $this->firestore = new FirestoreClient([
            'projectId' => 'poynt-app',
            'keyFilePath' => base_path('poynt-app-firebase-key-file.json')
        ]);
    }

    private function getCollection(string $name): CollectionReference
    {
        return $this->firestore->collection($name);
    }

    public function createOrUpdateDocument(string $documentId, string $collection, array $data): array
    {
        return $this->getCollection($collection)->document($documentId)->set($this->setTimestamp($data), ['merge' => true]);
    }

    private function setTimestamp(array $data): array
    {
        if (isset($data['createdAt'])) $data['ts'] = Carbon::parse($data['createdAt'])->timestamp;
        if (!isset($data['deletedAt'])) $data['deletedAt'] = null;
        return $data;
    }
}
