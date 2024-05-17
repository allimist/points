<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AutoPlayerRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class AutoPlayerCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AutoPlayerCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CloneOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\AutoPlayer::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/auto-player');
        CRUD::setEntityNameStrings('auto player', 'auto players');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('name');
        CRUD::column('user');
//        CRUD::column('tasks');
        CRUD::column('step');
        CRUD::column('next_state_on');
        CRUD::column('is_active');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(AutoPlayerRequest::class);

        CRUD::field('name');
        CRUD::field('user');
//        CRUD::field('tasks');
//        CRUD::field('tasks')->type('repeatable');
        $service = \App\Models\Service::all()->pluck('name', 'id')->toArray();
        $this->crud->addField([
            'name' => 'tasks',
            'type'  => 'repeatable',
            'subfields' => [
                [
                    'name'    => 'type',
                    'type'    => 'select2_from_array',
                    'options'    => ['move','teleport','wait'],
                    'wrapper' => ['class' => 'form-group col-md-2'],
                ],
                [
                    'name'    => 'posX',
                    'label'    => 'posX / Land ID (0=Random)',
                    'type'    => 'number',
                    'default' => 0,
                    'wrapper' => ['class' => 'form-group col-md-2'],
                ],
                [
                    'name'    => 'posY',
                    'label'    => 'posY (0=Random)',

//                    'options'    => $currencyOptions,
                    'default' => 0,
                    'wrapper' => ['class' => 'form-group col-md-2'],
                ],
                [
                    'name'    => 'service',
                    'type'    => 'select2_from_array',
                    'options'    => $service,
                    'wrapper' => ['class' => 'form-group col-md-3'],
                ],
                [
                    'name'    => 'seconds',
                    'type'    => 'time',
                    'default' => 10,
                    'wrapper' => ['class' => 'form-group col-md-3'],
                ],
            ],
            'new_item_label'  => 'Add Group',
            'init_rows' => 0,
            'max_rows' => 10,
        ]);

        CRUD::field('step');
        CRUD::field('next_state_on');
        CRUD::field('is_active');

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
