<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class DB
{
    private $_DB;
    private $_DBEXISTS;
    private $_TABLE;

    public function __construct($table)
    {
        $this->_DBEXISTS = false;
        $this->_DB = 'list-image';
        $this->_TABLE = $table;

        try {
            $exists = Storage::disk('db')->exists($this->getTable());
            if (!$exists) {
                throw new Exception('DB Not Found.');
            }
            $this->_DBEXISTS = true;
        } catch (Exception $exception) {
            $message = $exception->getMessage() ?? 'Unknown Error';
            Log::error($message);
        }
    }

    public function __toString()
    {
        return $this->_DB;
    }

    public function getTable()
    {
        return $this->_DB . '/' . $this->_TABLE;
    }

    public function exists()
    {
        return Storage::disk('db')->exists($this->getTable());
    }

    public function all($order_by = 'asc')
    {
        $file = Storage::disk('db')->get($this->getTable());
        $file = json_decode($file, true);
        $file = (!empty($file) && $order_by == 'desc') ? array_reverse($file) : $file;
        return $file;
    }

    public function insert(array $data)
    {
        $images = $this->all();
        $key = (!empty($images) && count($images) > 0) ? count($images) + 1 : 1;
        $f_data = (!empty($images) && count($images) > 0) ? $images : [];
        $data['id'] = $key;
        $f_data[$key] = $data;
        Storage::disk('db')->put($this->getTable(), json_encode(array_values($f_data)));
        return [$key => $data];
    }

    public function get(int $key)
    {
        $data = null;
        $images = $this->all();
        if (!empty($images) && count($images) > 0) {
            foreach ($images as $index => $image) {
                if ($image['id'] == $key) {
                    $data = $images[$index];
                    break;
                }
            }
        }
        return $data;
    }

    public function delete(int $key)
    {
        $images = $this->all();
        if (!empty($images) && count($images) > 0) {
            foreach ($images as $index => $image) {
                if ($image['id'] == $key) {
                    unset($images[$index]);
                    break;
                }
            }
            Storage::disk('db')->put($this->getTable(), json_encode(array_values($images)));
            return true;
        }
        return false;
    }
}
