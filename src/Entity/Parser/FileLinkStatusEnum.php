<?php

namespace App\Entity\Parser;

enum FileLinkStatusEnum: string {
    case NotDownloaded = 'not_downloaded';
    case Downloaded = 'downloaded';
    case Processed = 'processed';
}