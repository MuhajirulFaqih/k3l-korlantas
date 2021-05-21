<?php

namespace App\Transformers;

use App\Models\News;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class NewsTransformer extends TransformerAbstract
{
    private $preview = false;

    public function __construct($prev = false)
    {
        $this->preview = $prev;
    }

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
            'judul'   => $this->preview ? Str::limit($news->title) : $news->title,
            'date' => $news->date->toDateTimeString(),
            'content' => $news->content,
            'gambar'  => $news->image ? url($news->image) : null,
        ];
    }
}
