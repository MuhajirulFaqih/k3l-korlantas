<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Admin;

class AdminTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['auth'];
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Admin $admin)
    {
        return [
            'id' => $admin->id,
            'nama' => $admin->nama,
            'visiblity' => $admin->visible,
            'online' => $admin->status
        ];
    }

    public function includeAuth(Admin $admin){
        return $this->item($admin->auth, new UserTransformer());
    }
}
