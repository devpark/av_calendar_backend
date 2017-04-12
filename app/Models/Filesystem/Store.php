<?php

namespace App\Models\Filesystem;

use Illuminate\Filesystem\FilesystemManager as Filesystem;

class Store
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Store constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem->disk('company');
    }

    /**
     * @param int $company_id
     * @param int $project_id
     *
     * @return string, path to file
     */
    public function getPath($company_id, $project_id)
    {
        $directory = $company_id . '/projects/' . $project_id;

        if (! $this->filesystem->exists($directory)) {
            $this->filesystem->makeDirectory($directory);
        }

        return $directory;
    }

    /**
     * @param string $directory
     * @param string $storage_name
     *
     * @return bool
     */
    public function fileExists($directory, $storage_name)
    {
        return $this->filesystem->exists($directory . '/' . $storage_name);
    }
}
