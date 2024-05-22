<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AvatarRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class AvatarCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AvatarCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Avatar::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/avatar');
        CRUD::setEntityNameStrings('avatar', 'avatars');
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
//        CRUD::column('image')->type('image');
        CRUD::column('image')->type('image')->prefix('storage/');


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
        CRUD::setValidation(AvatarRequest::class);

        CRUD::field('name');
//        CRUD::field('image');

//        $this->crud->addField([
//            'label' => 'Image',
//            'name' => 'image',
//            'type' => 'image',
//            'crop' => true, // set to true to allow cropping, false to disable
//            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
//        ]);

//        $this->crud->addField([
//            'name' => 'image',
//            'type' => 'image_custom',
////            'type' => 'image',
////            'prefix' => 'storage/',
//            'upload' => true,
//        ]);

        $this->crud->addField([
            'name' => 'image', // The db column name where the image path is stored
            'type' => 'upload',
            'upload' => true
        ]);

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
