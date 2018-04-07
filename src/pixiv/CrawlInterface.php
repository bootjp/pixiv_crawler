<?php


namespace pixiv;

interface CrawlInterface
{
    public function __construct(string $id, string $password);
    public function login() :bool;
    public function fetchImageByTag(string $string): array;
}
