<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ProviderRepository;
use App\Entities\Provider;
use App\Validators\ProviderValidator;

/**
 * Class ProviderRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ProviderRepositoryEloquent extends BaseRepository implements ProviderRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Provider::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return ProviderValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
