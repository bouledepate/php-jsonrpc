<?php

declare(strict_types=1);

namespace Demo\BillApi;

use RuntimeException;

final readonly class BillStore
{
    public function __construct(private string $storagePath)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function load(): array
    {
        if (!file_exists($this->storagePath)) {
            return ['bills' => []];
        }

        $raw = file_get_contents($this->storagePath);
        if ($raw === false || $raw === '') {
            return ['bills' => []];
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded) || !array_key_exists('bills', $decoded)) {
            throw new RuntimeException('Invalid storage format.');
        }

        return $decoded;
    }

    /**
     * @param callable(array<string, mixed>):array<string, mixed> $callback
     *
     * @return array<string, mixed>
     */
    public function mutate(callable $callback): array
    {
        $dir = dirname($this->storagePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $handle = fopen($this->storagePath, 'c+');
        if ($handle === false) {
            throw new RuntimeException('Cannot open storage file.');
        }

        try {
            if (!flock($handle, LOCK_EX)) {
                throw new RuntimeException('Cannot lock storage file.');
            }

            $raw = stream_get_contents($handle);
            $state = ['bills' => []];

            if ($raw !== false && $raw !== '') {
                $decoded = json_decode($raw, true);
                if (is_array($decoded) && array_key_exists('bills', $decoded)) {
                    $state = $decoded;
                }
            }

            $updated = $callback($state);

            ftruncate($handle, 0);
            rewind($handle);
            fwrite($handle, json_encode($updated, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            fflush($handle);

            flock($handle, LOCK_UN);

            return $updated;
        } finally {
            fclose($handle);
        }
    }
}
