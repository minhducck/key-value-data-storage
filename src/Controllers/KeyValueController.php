<?php

declare(strict_types=1);

namespace Minhducck\KeyValueDataStorage\Controllers;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Minhducck\KeyValueDataStorage\Interfaces\KeyValueStorageServiceInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class KeyValueController extends Controller
{
    use AuthorizesRequests;
    use ValidatesRequests;

    public function __construct(private readonly KeyValueStorageServiceInterface $keyValueStorageService)
    {
    }

    /**
     * Getting all latest key-value pair from database.
     */
    public function getAll(): JsonResponse
    {
        return response()->json((object)$this->keyValueStorageService->getAllRecords());
    }

    /**
     * Retrieve versioned value by object key and timestamp if present.
     *
     * @param Request $request
     * @param string  $key
     * @return JsonResponse
     */
    public function retrieveByKey(Request $request, string $key): JsonResponse
    {
        $timestamp = $request->get('timestamp', null);
        if ($timestamp !== null) {
            $timestamp = (string)$timestamp;
        }

        return response()->json($this->keyValueStorageService->retrieve($key, $timestamp));
    }

    /**
     * Store Data into Database.
     */
    public function store(Request $request): JsonResponse
    {
        $dictionary = $request->post();
        if (!is_array($dictionary)) {
            throw new BadRequestHttpException("Invalid input");
        }

        $lastEventSaveTimestamp = $this->keyValueStorageService->save($dictionary);
        $timeAsString           = Carbon::createFromTimestampUTC($lastEventSaveTimestamp)
            ->toIso8601String();

        return response()->json([
            'time' => $timeAsString,
        ]);
    }
}
