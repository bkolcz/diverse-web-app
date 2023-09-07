<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Uid\UuidV7;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class MessageFileService implements MessageServiceInterface
{
    const DATE_FORMAT = 'Ymdhisv';
    const MESSAGE_FILENAME_FORMAT = '/(?<uuid>\w{8}(?:-\w{4}){3}-\w{12}).(?<time>\d{17}).(?<ext>json)/';
    const MESSAGE_KEY_FORMAT = '/(?<uuid>\w{8}(?:-\w{4}){3}-\w{12}).(?<time>\d{17})/';

    public function __construct(
        private ContainerBagInterface $params,
        private UuidV7 $uuid = new UuidV7(),
        private Filesystem $filesystem = new Filesystem(),
        private Finder $finder = new Finder(),
    ) {
        try {
            $filesystem->mkdir(
                Path::normalize($this->params->get('message_storage')),
            );
        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while creating your directory at " . $exception->getPath();
        }
    }
    public function save(string $message): string
    {
        $dateTime = $this->uuid->getDateTime();
        $tries = 3;
        $status = 'failed';
        do {
            $uuid = $this->uuid->generate($dateTime);
            $this->finder->files()->name($uuid . '*')->in($this->params->get('message_storage'));
            $tries -= 1;
        } while ($this->finder->hasResults() && $tries > 0);
        if (!$this->finder->hasResults()) {
            $this->filesystem->dumpFile(
                sprintf(
                    "%s/%s.%s.json",
                    $this->params->get('message_storage'),
                    $uuid,
                    $dateTime->format(self::DATE_FORMAT)
                ),
                $message
            );
            $status = $uuid;
        }
        return $status;
    }
    public function getAll(): array
    {
        $found = $this->finder->files()->name(self::MESSAGE_FILENAME_FORMAT)->in($this->params->get('message_storage'));
        $result = [];
        foreach ($found as $file) {
            $result[$file->getFilenameWithoutExtension()] = json_decode($file->getContents());
        }
        return $result;
    }
    public function getOne(string $uuid): array
    {
        $found = $this->finder->files()->name($uuid . '*')->in($this->params->get('message_storage'));
        $result = [];
        if ($this->finder->hasResults()) {
            foreach ($found as $file) {
                array_push($result, $file->getFilename());
            }
        }
        return $result;
    }

    public function sort(array $messageList, string $by, string $order): array
    {
        uksort($messageList, match(true) { 
            $by === 'name' && $order === 'asc' => [self::class, "uuidComparator"],
            $by === 'name' && $order === 'desc' => [self::class, "uuidReverseComparator"],
            $by === 'date' && $order === 'asc' => [self::class, "dateComparator"],
            $by === 'date' && $order === 'desc' => [self::class, "dateReverseComparator"],
            default => [self::class, "uuidComparator"],
        });
        return $messageList;
    }

    private static function uuidComparator($a, $b)
    {
        $aMatches = [];
        $bMatches = [];
        if (
            preg_match(self::MESSAGE_KEY_FORMAT, $a, $aMatches) &&
            preg_match(self::MESSAGE_KEY_FORMAT, $b, $bMatches)
        ) {
            return $aMatches['uuid'] <=> $bMatches['uuid'];
        }
        return 0;
    }
    private static function dateComparator($a, $b)
    {
        $aMatches = [];
        $bMatches = [];
        if (
            preg_match(self::MESSAGE_KEY_FORMAT, $a, $aMatches) &&
            preg_match(self::MESSAGE_KEY_FORMAT, $b, $bMatches)
        ) {
            return $aMatches['time'] <=> $bMatches['time'];
        }
        return 0;
    }

    private static function uuidReverseComparator($a, $b)
    {
        $aMatches = [];
        $bMatches = [];
        if (
            preg_match(self::MESSAGE_KEY_FORMAT, $a, $aMatches) &&
            preg_match(self::MESSAGE_KEY_FORMAT, $b, $bMatches)
        ) {
            return $bMatches['uuid'] <=> $aMatches['uuid'];
        }
        return 0;
    }
    private static function dateReverseComparator($a, $b)
    {
        $aMatches = [];
        $bMatches = [];
        if (
            preg_match(self::MESSAGE_KEY_FORMAT, $a, $aMatches) &&
            preg_match(self::MESSAGE_KEY_FORMAT, $b, $bMatches)
        ) {
            return $bMatches['time'] <=> $aMatches['time'];
        }
        return 0;
    }
}
