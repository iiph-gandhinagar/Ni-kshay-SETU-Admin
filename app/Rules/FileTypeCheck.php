<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Log;

class FileTypeCheck implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    protected $request;
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {

        // Log::info($this->request);
        // Log::info($this->request->material);
        $extension = "";
        // Log::info($this->request['type_of_materials']);
        if ($this->request['type_of_materials'] == 'folder') {
            return true;
        }
        if (isset($this->request['id'])) {
            if ($this->request['material'] && $this->request['material'][1]['path']) {
                $extension = pathinfo($this->request['material'][1]['path'], PATHINFO_EXTENSION);
            } else {
                return true;
            }
        } else {
            if ($this->request['type_of_materials'] != 'folder') {
                $extension = pathinfo($this->request['material'][0]['path'], PATHINFO_EXTENSION);
            }
        }

        if ($this->request['type_of_materials'] == 'pdfs' || $this->request['type_of_materials'] == 'pdf_office_orders') {
            if ($extension == 'pdf') {
                return true;
            }
            return false;
        } elseif ($this->request['type_of_materials'] == 'images') {
            if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg') {
                return true;
            }
            return false;
        } elseif ($this->request['type_of_materials'] == 'videos') {
            if ($extension == 'mp4' || $extension == 'MOV' || $extension == 'WMV' || $extension == 'm4v' || $extension == 'm4a') return true;
            return false;
        } elseif ($this->request['type_of_materials'] == 'ppt') {
            if ($extension == 'ppt' || $extension == 'pptx') return true;
            return false;
        } elseif ($this->request['type_of_materials'] == 'document') {
            if ($extension == 'doc' || $extension == 'docm' || $extension == 'docx' || $extension == 'dot') return true;
            return false;
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Please Upload proper media';
    }
}
