<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

trait Versionable
{
    /**
     * A list of attributes not to add to the version.
     *
     * @var array
     */
    private array $unversionedAttributes = ['versions','name','email'];
    /**
     * Indicates if the version attribute should be updated.
     *
     * @var bool
     */
    private bool $effectVersions = true;
    /**
     * Indicates if the version creation should be skipped.
     *
     * @var bool
     */
    private bool $skipVersion = false;

    /**
     * Runs after Eloquent Model is instantiated
     *
     * @return void
     */
    public function initializeVersionable()
    {
        data_set($this->casts, 'versions', 'object');

        if (isset($this->effectiveVersions)) {
            $this->effectVersions = is_bool($this->effectiveVersions) ? $this->effectiveVersions : $this->effectVersions;
        }
    }

    protected static function bootVersionable()
    {
        static::creating(function ($model) {
            $model->createVersion();
        });

        static::updating(function ($model) {
            $model->createVersion();
        });
    }

    /**
     * Create new version from the current model
     * @return array|null  created version
     */
    protected function createVersion(): ?object
    {

        if( $this->isVersionSkipped() ){
            $this->dontSkipVersion();
            return null;
        }

        if ($this->usesTimestamps()) {
            $this->updateTimestamps();
        }

        $newVersion = clone $this;
        $newVersion->makeHidden( $this->unversionableAttributes() );
        $newVersion->version = $this->nextVersionNumber();

        $versions = $this->getOriginal('versions') ?? $this->versions ?? [];

        array_push($versions, $newVersion);

        $this->versions = $versions;

        if ($this->isEffectiveVersion()) {
            $this->setEffectiveVersion();
        }

        return $newVersion;
    }

    /**
     * Set the active version (private)
     * @param int|null $number number of the version to set
     * @return bool|null        set version
     */
    protected function setEffectiveVersion(int $number = null): ?bool
    {
        return $this->version = $number ?? $this->nextVersionNumber()-1;
    }

    /**
     * Whether a new version comes into effect
     * @return bool|null
     */
    public function isEffectiveVersion(): ?bool
    {
        return $this->effectVersions ?? null;
    }

    /**
     * Check if the model has a version with a certain number
     * @param int $number version number
     * @return bool|null
     */
    public function hasVersion(int $number): ?bool
    {
        return !empty(collect($this->versions ?? [])->where('version', $number)->first());
    }

    /**
     * Get the next version number
     * @return int  the version number
     */
    protected function nextVersionNumber(): int
    {
        $versionMax = collect($this->versions ?? [])->max('version') ?? 0;
        return $versionMax+1;
    }

    /**
     * Check if the version creation is skipped
     * @return bool
     */
    public function isVersionSkipped(): bool
    {
        return $this->skipVersion;
    }

    /**
     * Get array of unversioned attributes
     * @return array
     */
    public function unversionableAttributes(): array
    {
        return array_merge($this->unversionedAttributes, $this->unversionable ?? []);
    }

    /**
     * Skip the next version creation
     * @return bool
     */
    public function skipVersion(): bool
    {
        return $this->skipVersion = true;
    }

    /**
     * Disable skipping the next version creation
     * @return bool
     */
    public function dontSkipVersion(): bool
    {
        return $this->skipVersion = false;
    }

    /**
     * Change the active version
     * @param int $number   number of the version to set
     * @return bool         result
     */
    public function changeVersion(int $number): bool
    {
        if ($this->hasVersion($number)) {
            return (bool)$this->setEffectiveVersion($number);
        }

        return false;
    }

    /**
     * Get a given or the active version
     * @param int|null $number
     * @return object|null
     */
    public function getVersion(int $number = null): ?object
    {
        $number = $number ?? $this->version ?? $this->nextVersionNumber()-1;

        return collect($this->versions ?? [])->where('version', $number)->first();
    }

    /**
     * Get the Model of a given or the active version
     * @param int|null $number number of the version to get
     * @return Model|null
     */
    public function getVersionModel(int $number = null): ?Model
    {
        if (!$object = $this->getVersion($number)) {
            return null;
        }

        $object->versions = $this->versions;

        $class = get_class($this);

        return new $class((array)$object);
    }

    /**
     * Get the most recent saved version
     * @param bool $toModel whether to cast the version to Model
     * @return object|Model|null
     */
    public function lastVersion(bool $toModel = false): mixed
    {
        if ($toModel) {
            return $this->getVersionModel(count($this->versions ?? []));
        }

        return $this->getVersion(count($this->versions ?? []));
    }

    /**
     * Revert the model to other version
     * @param int $number       number of the version to get
     * @param bool $skipVersion whether to skip or not the version creation
     * @return bool
     */
    public function revertToVersion(int $number, bool $skipVersion = false): bool
    {
        if (!$object = $this->getVersion($number)) {
            return false;
        }

        if ($skipVersion) {
            $this->skipVersion();
        }

        $newAttributes = array_diff_key($this->toArray(), array_flip($this->unversionableAttributes()));
        unset($newAttributes['id']);
        array_walk($newAttributes, function (&$value) {
            $value = null;
        });
        $newAttributes = array_merge($newAttributes, (array)$object);

        return $this->update( $newAttributes );
    }

}
