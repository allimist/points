<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
//use App\Models\Setting;


class SettingCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;


    public function setup()
    {

//        die;
        CRUD::setModel(config('backpack.settings.model', \Backpack\Settings\app\Models\Setting::class));
        CRUD::setEntityNameStrings(trans('backpack::settings.setting_singular'), trans('backpack::settings.setting_plural'));
        CRUD::setRoute(backpack_url(config('backpack.settings.route')));

        $this->crud->allowAccess('create');  // Allow creating new entries

    }

    public function setupListOperation()
    {
//        die;
        // only show settings which are marked as active
//        CRUD::addClause('where', 'active', 1);

        // columns to show in the table view
        CRUD::setColumns([
            [
                'name'  => 'key',
                'label' => trans('backpack::settings.key'),
            ],
            [
                'name'  => 'name',
                'label' => trans('backpack::settings.name'),
            ],
//            [
//                'name'  => 'type',
//                'label' => trans('backpack::settings.type'),
//            ],
            [
                'name'  => 'value',
                'label' => trans('backpack::settings.value'),
            ],
            [
                'name'  => 'description',
                'label' => trans('backpack::settings.description'),
            ],
            [
                'name'  => 'active',
                'label' => trans('backpack::settings.active'),
            ],
        ]);
    }


    public function setupCreateOperation()
    {


        CRUD::addField([
            'name'       => 'key',
        ]);

        CRUD::addField([
            'name'       => 'name',
//            'label'      => trans('backpack::settings.name'),
            'type'       => 'text',
//            'attributes' => [
//                'disabled' => 'disabled',
//            ],
        ]);

        CRUD::addField([
            'name'       => 'field',
        ]);

        CRUD::addField([
            'name'       => 'value',
        ]);



        CRUD::addField([
            'name'       => 'description',
        ]);

        CRUD::addField([
            'name'       => 'active',
        ]);

    }

    public function setupUpdateOperation()
    {
//        CRUD::addField([
//            'name'       => 'name',
//            'label'      => trans('backpack::settings.name'),
//            'type'       => 'text',
//            'attributes' => [
//                'disabled' => 'disabled',
//            ],
//        ]);

//        CRUD::addField(json_decode(CRUD::getCurrentEntry()->field, true));
        $this->setupCreateOperation();

    }
}
