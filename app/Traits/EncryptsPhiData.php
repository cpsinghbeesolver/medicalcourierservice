<?php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;

/**
 * Trait for encrypting Protected Health Information (PHI) data in compliance with HIPAA
 *
 * This trait automatically encrypts and decrypts specified PHI fields to ensure
 * data protection at rest as required by HIPAA Security Rule.
 */
trait EncryptsPhiData
{
    /**
     * Boot the trait
     */
    protected static function bootEncryptsPhiData()
    {
        // Encrypt PHI fields before saving
        static::saving(function ($model) {
            $model->encryptPhiAttributes();
        });

        // Decrypt PHI fields after retrieving
        static::retrieved(function ($model) {
            $model->decryptPhiAttributes();
        });
    }

    /**
     * Get the list of PHI fields that should be encrypted
     *
     * @return array
     */
    public function getEncryptedPhiFields(): array
    {
        return property_exists($this, 'encryptedPhiFields') ? $this->encryptedPhiFields : [];
    }

    /**
     * Encrypt PHI attributes before saving to database
     *
     * @return void
     */
    protected function encryptPhiAttributes(): void
    {
        // print_r($this->getEncryptedPhiFields());die;
        foreach ($this->getEncryptedPhiFields() as $field) {
            if (isset($this->attributes[$field]) && !empty($this->attributes[$field])) {
                // Check if already encrypted (contains encryption prefix)
                if (!$this->isEncrypted($this->attributes[$field])) {
                    $this->attributes[$field] = Crypt::encryptString($this->attributes[$field]);
                }
            }
        }
    }

    /**
     * Decrypt PHI attributes after retrieving from database
     *
     * @return void
     */
    protected function decryptPhiAttributes(): void
    {
        foreach ($this->getEncryptedPhiFields() as $field) {
            if (isset($this->attributes[$field]) && !empty($this->attributes[$field])) {
                if ($this->isEncrypted($this->attributes[$field])) {
                    try {
                        $this->attributes[$field] = Crypt::decryptString($this->attributes[$field]);
                    } catch (\Exception $e) {
                        // Log decryption failure for audit purposes
                        \Log::error('PHI Decryption Failed', [
                            'model' => get_class($this),
                            'field' => $field,
                            'id' => $this->id ?? 'unknown',
                            'error' => $e->getMessage()
                        ]);
                        $this->attributes[$field] = null;
                    }
                }
            }
        }
    }

    /**
     * Check if a value is already encrypted
     *
     * @param string $value
     * @return bool
     */
    protected function isEncrypted(string $value): bool
    {
        // Laravel's Crypt uses base64 encoding and starts with 'eyJpdiI6'
        return strpos($value, 'eyJpdiI6') === 0 ||
               strpos($value, 'eyJtYWMi') === 0;
    }

    /**
     * Set an encrypted attribute (for use in forms/requests)
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setEncryptedAttribute(string $key, $value): void
    {
        if (in_array($key, $this->getEncryptedPhiFields())) {
            $this->attributes[$key] = $value;
        } else {
            $this->setAttribute($key, $value);
        }
    }

    /**
     * Get a decrypted attribute value
     *
     * @param string $key
     * @return mixed
     */
    public function getDecryptedAttribute(string $key)
    {
        return $this->attributes[$key] ?? null;
    }
}
