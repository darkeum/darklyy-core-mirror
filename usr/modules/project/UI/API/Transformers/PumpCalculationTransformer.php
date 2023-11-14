<?php

namespace Calculation\UI\API\Transformers;

use \Boot\Abstracts\Transformers\Transformer;

class PumpCalculationTransformer extends Transformer
{
    protected $availableIncludes = [
        'images',
    ];

    protected $defaultIncludes = [
        'roles',
    ];

    public function transform(Item $item)
    {
        $response = [
            'object'      => $item->getResourceKey(),
            'id'          => $item->getHashedKey(),
            'name'        => $item->name,
            'description' => $item->description,
            'price'       => (float)$item->price,
            'weight'      => (float)$item->weight,
            'created_at'  => $item->created_at,
            'updated_at'  => $item->updated_at,
            'readable_created_at' => $item->created_at->diffForHumans(),
            'readable_updated_at' => $item->updated_at->diffForHumans(),
        ];

        // add more or modify data for Admins only
        $response = $this->ifAdmin([
            'real_id'    => $item->id,
            'deleted_at' => $item->deleted_at,
        ], $response);

        return $response;
    }

    public function includeImages(Item $item)
    {
        return $this->collection($item->images, new ItemImageTransformer());
    }

    public function includeRoles(User $user)
    {
        return $this->collection($user->roles, new RoleTransformer());
    }
}
