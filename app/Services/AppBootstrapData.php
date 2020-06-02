<?php namespace App\Services;

use Common\Core\Bootstrap\BaseBootstrapData;

class AppBootstrapData extends BaseBootstrapData
{
    public function init()
    {
        parent::init();
        $this->data['tags'] = app(TagRepository::class)->getStatusAndCategoryTags();
        return $this;
    }
}
