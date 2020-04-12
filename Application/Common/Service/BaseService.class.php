<?php
namespace Common\Service;

interface BaseService
{
    public function add($data) ;
    public function delete($id) ;
    public function update($data,$id) ;
    public function getList($data,$page,$limit) ;
}

?>