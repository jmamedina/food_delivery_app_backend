<?php

namespace App\Admin\Controllers;

use App\Models\FoodType;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;
use Encore\Admin\Tree;

class FoodTypeController extends AdminController
{
    /**
     * Food Type Controller
     * 食品タイプコントローラー
     * 
     * Handles CRUD operations for managing food types.
     * 食品タイプの管理のための CRUD 操作を処理します。
     *
     * @var string
     */
    protected $title = 'Food Type';

    /**
     * Display a listing of the resource.
     * リソースの一覧を表示する。
     *
     * @param  \Encore\Admin\Layout\Content  $content
     * @return \Encore\Admin\Layout\Content
     */
    public function index(Content $content)
    {
        $tree = new Tree(new FoodType);

        return $content
            ->header('Food Type')
            ->body($tree);
    }

    /**
     * Make a show builder.
     * ショービルダーを作成する。
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(FoodType::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));
        $show->field('description', __('Description'));
        $show->field('order', __('Order'));
        $show->field('created_at', __('Created_at'));
        $show->field('updated_at', __('Updated_at'));

        return $show;
    }

    /**
     * Make a form builder.
     * フォームビルダーを作成する。
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new FoodType());
        $form->select('parent_id', __('Parent Category'))->options((new FoodType())::selectOptions());
        $form->text('title', __('Title'));
        $form->textarea('description', __('Description'));
        $form->number('order', __('Order'))->default(1);

        return $form;
    }
}
