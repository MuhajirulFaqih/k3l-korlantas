<?php

namespace App\Transformers;

use App\Models\Admin;
use League\Fractal\TransformerAbstract;

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
            'visibility' => $admin->visible,
            'online' => $admin->status
        ];
    }

    public function includeAuth(Admin $admin){
        return $this->item($admin->auth, new UserTransformer());
    }
}
