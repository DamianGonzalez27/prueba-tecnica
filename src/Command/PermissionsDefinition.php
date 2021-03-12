<?php

namespace App\Command;
class PermissionsDefinition
{
    const ROLES = [
        ['name' => 'SUPER_ADMIN', 'title' => 'Súper administrador', 'assignable' => false],
        ['name' => 'ROLE_GENERAL_ADMIN', 'title' => 'Administrador normal', 'assignable' => true]
    ];

    const PERMISSIONS = [

        ['name' => 'USER_LIST', 'title' => 'Listar los usuarios'],
        ['name' => 'USER_LIST_SIMPLE', 'title' => 'Listar los usuarios con información mínima'],
        ['name' => 'USER_CREATE', 'title' => 'Crear un usuario'],
        ['name' => 'USER_UPDATE', 'title' => 'Actualizar un usuario'],
        ['name' => 'USER_DELETE', 'title' => 'Eliminar un usuario'],
        ['name' => 'USER_SHOW', 'title' => 'Ver un usuario'],

        ['name' => 'ROLES_LIST', 'title' => 'Listar los roles'],
        ['name' => 'ROLES_SHOW', 'title' => 'Ver un rol'],
        
        ['name' => 'CART_SHOW', 'title' => 'Ver un carrito'],
        ['name' => 'CART_LIST', 'title' => 'Listar los carritos'],
        ['name' => 'CART_CREATE', 'title' => 'Crear un carrito'],
        ['name' => 'CART_UPDATE', 'title' => 'Actualizar un carrito'],
        ['name' => 'CART_DELETE', 'title' => 'Eliminar un carrito'],

        ['name' => 'LOAN_SHOW', 'title' => 'Ver un carrito'],
        ['name' => 'LOAN_LIST', 'title' => 'Listar los carritos'],
        ['name' => 'LOAN_CREATE', 'title' => 'Crear un carrito'],
        ['name' => 'LOAN_UPDATE', 'title' => 'Actualizar un carrito'],
        ['name' => 'LOAN_DELETE', 'title' => 'Eliminar un carrito'],

        ['name' => 'ORDER_SHOW', 'title' => 'Ver un carrito'],
        ['name' => 'ORDER_LIST', 'title' => 'Listar los carritos'],
        ['name' => 'ORDER_CREATE', 'title' => 'Crear un carrito'],
        ['name' => 'ORDER_UPDATE', 'title' => 'Actualizar un carrito'],
        ['name' => 'ORDER_DELETE', 'title' => 'Eliminar un carrito'],

        ['name' => 'ORDER_SHOW', 'title' => 'Ver un carrito'],
        ['name' => 'ORDER_LIST', 'title' => 'Listar los carritos'],
        ['name' => 'ORDER_CREATE', 'title' => 'Crear un carrito'],
        ['name' => 'ORDER_UPDATE', 'title' => 'Actualizar un carrito'],
        ['name' => 'ORDER_DELETE', 'title' => 'Eliminar un carrito'],

        ['name' => 'PAYMENT_SHOW', 'title' => 'Ver un carrito'],
        ['name' => 'PAYMENT_LIST', 'title' => 'Listar los carritos'],
        ['name' => 'PAYMENT_CREATE', 'title' => 'Crear un carrito'],
        ['name' => 'PAYMENT_UPDATE', 'title' => 'Actualizar un carrito'],
        ['name' => 'PAYMENT_DELETE', 'title' => 'Eliminar un carrito'],

        ['name' => 'PAYMENT_CATEGORY_SHOW', 'title' => 'Ver un carrito'],
        ['name' => 'PAYMENT_CATEGORY_LIST', 'title' => 'Listar los carritos'],
        ['name' => 'PAYMENT_CATEGORY_CREATE', 'title' => 'Crear un carrito'],
        ['name' => 'PAYMENT_CATEGORY_UPDATE', 'title' => 'Actualizar un carrito'],
        ['name' => 'PAYMENT_CATEGORY_DELETE', 'title' => 'Eliminar un carrito'],

        ['name' => 'PAYMENT_METHOD_SHOW', 'title' => 'Ver un carrito'],
        ['name' => 'PAYMENT_METHOD_LIST', 'title' => 'Listar los carritos'],
        ['name' => 'PAYMENT_METHOD_CREATE', 'title' => 'Crear un carrito'],
        ['name' => 'PAYMENT_METHOD_UPDATE', 'title' => 'Actualizar un carrito'],
        ['name' => 'PAYMENT_METHOD_DELETE', 'title' => 'Eliminar un carrito'],

        ['name' => 'PRODUCT_SHOW', 'title' => 'Ver un carrito'],
        ['name' => 'PRODUCT_LIST', 'title' => 'Listar los carritos'],
        ['name' => 'PRODUCT_CREATE', 'title' => 'Crear un carrito'],
        ['name' => 'PRODUCT_UPDATE', 'title' => 'Actualizar un carrito'],
        ['name' => 'PRODUCT_DELETE', 'title' => 'Eliminar un carrito'],

        ['name' => 'PRODUCT_CART_SHOW', 'title' => 'Ver un carrito'],
        ['name' => 'PRODUCT_CART_LIST', 'title' => 'Listar los carritos'],
        ['name' => 'PRODUCT_CART_CREATE', 'title' => 'Crear un carrito'],
        ['name' => 'PRODUCT_CART_UPDATE', 'title' => 'Actualizar un carrito'],
        ['name' => 'PRODUCT_CART_DELETE', 'title' => 'Eliminar un carrito'],

        ['name' => 'PRODUCT_PRICE_SHOW', 'title' => 'Ver un carrito'],
        ['name' => 'PRODUCT_PRICE_LIST', 'title' => 'Listar los carritos'],
        ['name' => 'PRODUCT_PRICE_CREATE', 'title' => 'Crear un carrito'],
        ['name' => 'PRODUCT_PRICE_UPDATE', 'title' => 'Actualizar un carrito'],
        ['name' => 'PRODUCT_PRICE_DELETE', 'title' => 'Eliminar un carrito'],

        ['name' => 'PROFILE_SHOW', 'title' => 'Ver un carrito'],
        ['name' => 'PROFILE_LIST', 'title' => 'Listar los carritos'],
        ['name' => 'PROFILE_CREATE', 'title' => 'Crear un carrito'],
        ['name' => 'PROFILE_UPDATE', 'title' => 'Actualizar un carrito'],
        ['name' => 'PROFILE_DELETE', 'title' => 'Eliminar un carrito'],

        ['name' => 'PROVIDER_SHOW', 'title' => 'Ver un carrito'],
        ['name' => 'PROVIDER_LIST', 'title' => 'Listar los carritos'],
        ['name' => 'PROVIDER_CREATE', 'title' => 'Crear un carrito'],
        ['name' => 'PROVIDER_UPDATE', 'title' => 'Actualizar un carrito'],
        ['name' => 'PROVIDER_DELETE', 'title' => 'Eliminar un carrito'],

        ['name' => 'SETTINGS_SHOW', 'title' => 'Ver un carrito'],
        ['name' => 'SETTINGS_LIST', 'title' => 'Listar los carritos'],
        ['name' => 'SETTINGS_CREATE', 'title' => 'Crear un carrito'],
        ['name' => 'SETTINGS_UPDATE', 'title' => 'Actualizar un carrito'],
        ['name' => 'SETTINGS_DELETE', 'title' => 'Eliminar un carrito'],

        ['name' => 'STOCK_INPUT_SHOW', 'title' => 'Ver un carrito'],
        ['name' => 'STOCK_INPUT_LIST', 'title' => 'Listar los carritos'],
        ['name' => 'STOCK_INPUT_CREATE', 'title' => 'Crear un carrito'],
        ['name' => 'STOCK_INPUT_UPDATE', 'title' => 'Actualizar un carrito'],
        ['name' => 'STOCK_INPUT_DELETE', 'title' => 'Eliminar un carrito'],

        ['name' => 'STOCK_INPUT_PRODUCT_SHOW', 'title' => 'Ver un carrito'],
        ['name' => 'STOCK_INPUT_PRODUCT_LIST', 'title' => 'Listar los carritos'],
        ['name' => 'STOCK_INPUT_PRODUCT_CREATE', 'title' => 'Crear un carrito'],
        ['name' => 'STOCK_INPUT_PRODUCT_UPDATE', 'title' => 'Actualizar un carrito'],
        ['name' => 'STOCK_INPUT_PRODUCT_DELETE', 'title' => 'Eliminar un carrito'],

    ];

    // -------- ROLE PERMISSION ------------

    public const ROLE_PERMISSIONS = [
        [
            "role"=>"ROLE_GENERAL_ADMIN",
            "permissions" => [
                'USER_LIST',
                'USER_CREATE',
                'USER_UPDATE',
                'USER_DELETE',
                'USER_SHOW',
            ]
        ]
    ];

    // Se utiliza para crear grupos de permisos
    const PERMISSION_GROUPS = [
        ['name' => 'Listar usuarios',
            'code' => 'LIST_ALL_USERS',
            'description' => 'Permite listar usuarios del sisitema',
            'permissions' => [
                'USER_LIST',
                'USER_SHOW'
            ]
        ]
    ];

}

