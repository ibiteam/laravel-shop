<?php

namespace App\Components;

use App\Exceptions\BusinessException;

interface ComponentImp
{
    public function setName(string $name);

    /**
     * 组件icon信息
     * @return array
     */
    public function icon(): array;

    /**
     * 组件参数
     * @return mixed
     */
    public function parameter(): array;

    /**
     * 组件验证规则
     * @param array $data
     * @return array
     * @throws BusinessException
     */
    public function validate(array $data): array;

    /**
     * 组件数据
     * @param array $data
     * @return array
     */
    public function getContent(array $data): array;

    /**
     * 后台渲染数据
     * @param array $data
     * @return array
     */
    public function display(array $data): array;


}
