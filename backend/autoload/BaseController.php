<?php

class BaseController
{
//    transform mapper to array to avoid data protected error
    protected function castMapperToArray($mapper) {
        $result = [];
        foreach ($mapper as $item) {
            $result[] = $item->cast();
        }
        return $result;
    }
}