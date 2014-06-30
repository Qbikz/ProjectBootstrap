<?php

class Qbikz_ProjectBootstrap_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * [_readCsv description]
     * @param  string $fileName     [description]
     * @param  array  $additional   [description]
     * @param  string $identifier   [description]
     * @return array                [description]
     */
    public function loadCsv($fileName, array $additional = array(), $identifier = null)
    {
        # Check file existence
        if (! file_exists($fileName)) {
            Mage::throwException(sprintf('File does not exist: %s', $fileName));
        }

        # Open file
        $fp = fopen($fileName, 'r');
        if (! $fp) {
            Mage::throwException(sprintf('Unable to read file: %s', $fileName));
        }

        # Gather results
        $result     = array();
        $keys       = array();
        $current    = null;

        # Loop CSV file, maybe create iterator?
        while (false !== ($data = fgetcsv($fp))) {

            # Trim values
            $data = array_map('trim', $data);
            if (empty($keys)) {
                $keys = $this->_getKeysForCsv($data);
                continue;
            }

            # Set row data based on keys and their length
            $row = array();
            foreach ($keys as $key => $count) {
                if ($count > 1) {
                    $row[$key] = array();
                    for ($i = 0; $i < $count; $i += 1) {
                        $row[$key][] = array_shift($data);
                    }
                    $row[$key] = array_filter($row[$key]);
                } else {
                    $row[$key] = array_shift($data);
                }
            }

            # Extract additional data and create seperate array
            if (! empty($additional)) {
                $row = $this->_extractAdditionalData($row, $additional);
            }

            if (null !== $identifier) {
                if (! empty($row[$identifier])) {
                    $current = $row[$identifier];
                }
                if (isset($result[$current])) {
                    $result[$current] = $this->_mergeRows($result[$current], $row);
                } else {
                    $result[$current] = $row;
                }
            } else {
                $result[] = $row;
            }
        }
        return $result;
    }

    /**
     * [_mergeRows description]
     * @param  array  $original   [description]
     * @param  array  $row        [description]
     * @param  array  $additional [description]
     * @return [type]             [description]
     */
    private function _mergeRows(array $original, array $row)
    {
        $row = array_filter($row);
        foreach ($row as $key => $value) {
            if (empty($value)) {
                continue;
            }
            if (! is_array($original[$key])) {
                $original[$key] = array($original[$key]);
            }
            if (is_array($value) && isset($value[0])) {
                $original[$key][] = $value[0];
            } else {
                $original[$key][] = $value;
            }
        }
        return $original;
    }

    /**
     * Extract extra data
     *
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    private function _extractAdditionalData(array $data, array $additional)
    {
        $result = array();
        foreach ($data as $key => $value) {
            if (substr($key, 0, 1) === '_') {
                $parts  = explode('_', $key);
                $group  = implode('_', array_slice($parts, 1, 1));
                if (in_array($group, $additional)) {
                    $result[$group][0][implode('_', array_slice($parts, 2))] = $value;
                } else {
                    $result[$key] = $value;
                }
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * [_getKeys description]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    private function _getKeysForCsv(array $data)
    {
        $keys = array();
        $key = null;
        foreach ($data as $item) {
            if ($item) {
                $key = $item;
                $keys[$key] = 1;
            } else {
                $keys[$key]++;
            }
        }
        return $keys;
    }
}
