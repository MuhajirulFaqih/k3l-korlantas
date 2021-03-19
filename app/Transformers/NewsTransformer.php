<?php

namespace App\Transformers;

use App\Models\News;
use League\Fractal\TransformerAbstract;

class NewsTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @param News $news
     * @return array
     */
    public function transform(News $news)
    {
        return [
            'id' => $news->id,
            'judul'   => $news->title,
            'date' => $news->date->toDateTimeString(),
            'content' => $news->content, 
            'gambar'  => $news->image ? url($news->image) : null,
        ];
    }
}
