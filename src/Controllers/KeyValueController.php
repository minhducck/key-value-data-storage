<?php

declare(strict_types=1);

namespace Minhducck\KeyValueDataStorage\Controllers;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Minhducck\KeyValueDataStorage\Exceptions\InvalidInputException;
use Minhducck\KeyValueDataStorage\Interfaces\KeyValueStorageServiceInterface;

class KeyValueController extends Controller
{
    use AuthorizesRequests;
    use ValidatesRequests;

    public function __construct(private readonly KeyValueStorageServiceInterface $keyValueStorageService) {}

    /**
     * Getting all latest key-value pair from database.
     */
    public function getAll(): JsonResponse
    {
        return response()->json((object) $this->keyValueStorageService->getAllRecords());
    }

    /**
     * Retrieve versioned value by object key and timestamp if present.
     */
    public function retrieveByKey(Request $request, string $key): JsonResponse
    {
        $timestamp = $request->get('timestamp', null);
        if ($timestamp !== null) {
            $timestamp = (string) $timestamp;
        }

        return response()->json($this->keyValueStorageService->retrieve($key, $timestamp));
    }

    /**
     * Store Data into Database.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $dictionary = $request->json()->all();
        } catch (\Exception $exception) {
            throw new InvalidInputException($exception->getMessage());
        }

        $lastEventSaveTimestamp = $this->keyValueStorageService->save($dictionary);
        $timeAsString = Carbon::createFromTimestampUTC($lastEventSaveTimestamp)
            ->toIso8601String();

        return response()->json([
            'time' => $timeAsString,
        ]);
    }
}
