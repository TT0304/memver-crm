<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeSeeder extends Seeder
{
    public function run()
    {
        DB::table('attributes')->delete();

        $now = Carbon::now();

        DB::table('attributes')->insert([
            [
                'id'              => '1',
                'code'            => 'title',
                'name'            => 'Title',
                'type'            => 'text',
                'entity_type'     => 'leads',
                'lookup_type'     => NULL,
                'validation'      => NULL,
                'sort_order'      => '1',
                'is_required'     => '1',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '2',
                'code'            => 'description',
                'name'            => 'Description',
                'type'            => 'textarea',
                'entity_type'     => 'leads',
                'lookup_type'     => NULL,
                'validation'      => NULL,
                'sort_order'      => '2',
                'is_required'     => '0',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '3',
                'code'            => 'lead_value',
                'name'            => 'Lead Value',
                'type'            => 'price',
                'entity_type'     => 'leads',
                'lookup_type'     => NULL,
                'validation'      => NULL,
                'sort_order'      => '3',
                'is_required'     => '1',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '4',
                'code'            => 'lead_source_id',
                'name'            => 'Source',
                'type'            => 'select',
                'entity_type'     => 'leads',
                'lookup_type'     => 'lead_sources',
                'validation'      => NULL,
                'sort_order'      => '4',
                'is_required'     => '1',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '5',
                'code'            => 'lead_type_id',
                'name'            => 'Type',
                'type'            => 'select',
                'entity_type'     => 'leads',
                'lookup_type'     => 'lead_types',
                'validation'      => NULL,
                'sort_order'      => '5',
                'is_required'     => '1',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '6',
                'code'            => 'user_id',
                'name'            => 'Sales Owner',
                'type'            => 'select',
                'entity_type'     => 'leads',
                'lookup_type'     => 'users',
                'validation'      => NULL,
                'sort_order'      => '7',
                'is_required'     => '0',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '7',
                'code'            => 'expected_close_date',
                'name'            => 'Expected Close Date',
                'type'            => 'date',
                'entity_type'     => 'leads',
                'lookup_type'     => NULL,
                'validation'      => NULL,
                'sort_order'      => '8',
                'is_required'     => '0',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '9',
                'code'            => 'name',
                'name'            => 'Name',
                'type'            => 'text',
                'entity_type'     => 'persons',
                'lookup_type'     => NULL,
                'validation'      => NULL,
                'sort_order'      => '1',
                'is_required'     => '1',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '10',
                'code'            => 'emails',
                'name'            => 'Emails',
                'type'            => 'email',
                'entity_type'     => 'persons',
                'lookup_type'     => NULL,
                'validation'      => NULL,
                'sort_order'      => '2',
                'is_required'     => '1',
                'is_unique'       => '1',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '11',
                'code'            => 'contact_numbers',
                'name'            => 'Contact Numbers',
                'type'            => 'phone',
                'entity_type'     => 'persons',
                'lookup_type'     => NULL,
                'validation'      => 'numeric',
                'sort_order'      => '3',
                'is_required'     => '0',
                'is_unique'       => '1',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '12',
                'code'            => 'organization_id',
                'name'            => 'Organization',
                'type'            => 'lookup',
                'entity_type'     => 'persons',
                'lookup_type'     => 'organizations',
                'validation'      => NULL,
                'sort_order'      => '4',
                'is_required'     => '0',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '13',
                'code'            => 'name',
                'name'            => 'Name',
                'type'            => 'text',
                'entity_type'     => 'organizations',
                'lookup_type'     => NULL,
                'validation'      => NULL,
                'sort_order'      => '1',
                'is_required'     => '1',
                'is_unique'       => '1',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '14',
                'code'            => 'address',
                'name'            => 'Address',
                'type'            => 'address',
                'entity_type'     => 'organizations',
                'lookup_type'     => NULL,
                'validation'      => NULL,
                'sort_order'      => '2',
                'is_required'     => '0',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '15',
                'code'            => 'name',
                'name'            => 'Name',
                'type'            => 'text',
                'entity_type'     => 'products',
                'lookup_type'     => NULL,
                'validation'      => NULL,
                'sort_order'      => '1',
                'is_required'     => '1',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '16',
                'code'            => 'description',
                'name'            => 'Description',
                'type'            => 'textarea',
                'entity_type'     => 'products',
                'lookup_type'     => NULL,
                'validation'      => NULL,
                'sort_order'      => '2',
                'is_required'     => '0',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '17',
                'code'            => 'sku',
                'name'            => 'SKU',
                'type'            => 'text',
                'entity_type'     => 'products',
                'lookup_type'     => NULL,
                'validation'      => NULL,
                'sort_order'      => '3',
                'is_required'     => '1',
                'is_unique'       => '1',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '18',
                'code'            => 'quantity',
                'name'            => 'Quantity',
                'type'            => 'text',
                'entity_type'     => 'products',
                'lookup_type'     => NULL,
                'validation'      => 'numeric',
                'sort_order'      => '4',
                'is_required'     => '1',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '19',
                'code'            => 'price',
                'name'            => 'Price',
                'type'            => 'text',
                'entity_type'     => 'products',
                'lookup_type'     => NULL,
                'validation'      => 'decimal',
                'sort_order'      => '5',
                'is_required'     => '1',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '20',
                'code'            => 'user_id',
                'name'            => 'Sales Owner',
                'type'            => 'select',
                'entity_type'     => 'quotes',
                'lookup_type'     => 'users',
                'validation'      => NULL,
                'sort_order'      => '1',
                'is_required'     => '1',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '21',
                'code'            => 'subject',
                'name'            => 'Subject',
                'type'            => 'text',
                'entity_type'     => 'quotes',
                'lookup_type'     => NULL,
                'validation'      => NULL,
                'sort_order'      => '2',
                'is_required'     => '1',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '22',
                'code'            => 'description',
                'name'            => 'Description',
                'type'            => 'textarea',
                'entity_type'     => 'quotes',
                'lookup_type'     => NULL,
                'validation'      => NULL,
                'sort_order'      => '3',
                'is_required'     => '0',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '23',
                'code'            => 'billing_address',
                'name'            => 'Billing Address',
                'type'            => 'address',
                'entity_type'     => 'quotes',
                'lookup_type'     => NULL,
                'validation'      => NULL,
                'sort_order'      => '4',
                'is_required'     => '1',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '24',
                'code'            => 'shipping_address',
                'name'            => 'Shipping Address',
                'type'            => 'address',
                'entity_type'     => 'quotes',
                'lookup_type'     => NULL,
                'validation'      => NULL,
                'sort_order'      => '5',
                'is_required'     => '0',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '25',
                'code'            => 'discount_percent',
                'name'            => 'Discount Percent',
                'type'            => 'text',
                'entity_type'     => 'quotes',
                'lookup_type'     => NULL,
                'validation'      => 'decimal',
                'sort_order'      => '6',
                'is_required'     => '0',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '26',
                'code'            => 'discount_amount',
                'name'            => 'Discount Amount',
                'type'            => 'price',
                'entity_type'     => 'quotes',
                'lookup_type'     => NULL,
                'validation'      => 'decimal',
                'sort_order'      => '7',
                'is_required'     => '0',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '27',
                'code'            => 'tax_amount',
                'name'            => 'Tax Amount',
                'type'            => 'price',
                'entity_type'     => 'quotes',
                'lookup_type'     => NULL,
                'validation'      => 'decimal',
                'sort_order'      => '8',
                'is_required'     => '0',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '28',
                'code'            => 'adjustment_amount',
                'name'            => 'Adjustment Amount',
                'type'            => 'price',
                'entity_type'     => 'quotes',
                'lookup_type'     => NULL,
                'validation'      => 'decimal',
                'sort_order'      => '9',
                'is_required'     => '0',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '29',
                'code'            => 'sub_total',
                'name'            => 'Sub Total',
                'type'            => 'price',
                'entity_type'     => 'quotes',
                'lookup_type'     => NULL,
                'validation'      => 'decimal',
                'sort_order'      => '10',
                'is_required'     => '1',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '30',
                'code'            => 'grand_total',
                'name'            => 'Grand Total',
                'type'            => 'price',
                'entity_type'     => 'quotes',
                'lookup_type'     => NULL,
                'validation'      => 'decimal',
                'sort_order'      => '11',
                'is_required'     => '1',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '31',
                'code'            => 'expired_at',
                'name'            => 'Expired At',
                'type'            => 'date',
                'entity_type'     => 'quotes',
                'lookup_type'     => NULL,
                'validation'      => NULL,
                'sort_order'      => '12',
                'is_required'     => '1',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '32',
                'code'            => 'person_id',
                'name'            => 'Person',
                'type'            => 'lookup',
                'entity_type'     => 'quotes',
                'lookup_type'     => 'persons',
                'validation'      => NULL,
                'sort_order'      => '13',
                'is_required'     => '1',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '33',
                'code'            => 'reply_to',
                'name'            => 'To',
                'type'            => 'email_tags',
                'entity_type'     => 'emails',
                'lookup_type'     => NULL,
                'validation'      => NULL,
                'sort_order'      => '1',
                'is_required'     => '1',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '34',
                'code'            => 'cc',
                'name'            => 'Cc',
                'type'            => 'email_tags',
                'entity_type'     => 'emails',
                'lookup_type'     => NULL,
                'validation'      => NULL,
                'sort_order'      => '2',
                'is_required'     => '0',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '35',
                'code'            => 'bcc',
                'name'            => 'Bcc',
                'type'            => 'email_tags',
                'entity_type'     => 'emails',
                'lookup_type'     => NULL,
                'validation'      => NULL,
                'sort_order'      => '3',
                'is_required'     => '0',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '36',
                'code'            => 'subject',
                'name'            => 'Subject',
                'type'            => 'text',
                'entity_type'     => 'emails',
                'lookup_type'     => NULL,
                'validation'      => NULL,
                'sort_order'      => '4',
                'is_required'     => '1',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '37',
                'code'            => 'reply',
                'name'            => 'Reply',
                'type'            => 'textarea_custom',
                'entity_type'     => 'emails',
                'lookup_type'     => NULL,
                'validation'      => NULL,
                'sort_order'      => '5',
                'is_required'     => '1',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '38',
                'code'            => 'source',
                'name'            => '',
                'type'            => 'attachment',
                'entity_type'     => 'emails',
                'lookup_type'     => NULL,
                'validation'      => NULL,
                'sort_order'      => '6',
                'is_required'     => '0',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '39',
                'code'            => 'name',
                'name'            => 'Name',
                'type'            => 'text',
                'entity_type'     => 'email_templates',
                'lookup_type'     => NULL,
                'validation'      => NULL,
                'sort_order'      => '1',
                'is_required'     => '1',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '40',
                'code'            => 'subject',
                'name'            => 'Subject',
                'type'            => 'subject',
                'entity_type'     => 'email_templates',
                'lookup_type'     => NULL,
                'validation'      => NULL,
                'sort_order'      => '2',
                'is_required'     => '1',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '41',
                'code'            => 'content',
                'name'            => 'Content',
                'type'            => 'textarea_custom',
                'entity_type'     => 'email_templates',
                'lookup_type'     => NULL,
                'validation'      => NULL,
                'sort_order'      => '3',
                'is_required'     => '1',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '42',
                'code'            => 'name',
                'name'            => 'Title',
                'type'            => 'text',
                'entity_type'     => 'permissions',
                'lookup_type'     => NULL,
                'validation'      => NULL,
                'sort_order'      => '1',
                'is_required'     => '1',
                'is_unique'       => '1',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '43',
                'code'            => 'name',
                'name'            => 'Name',
                'type'            => 'text',
                'entity_type'     => 'users',
                'lookup_type'     => NULL,
                'validation'      => NULL,
                'sort_order'      => '1',
                'is_required'     => '1',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '44',
                'code'            => 'email',
                'name'            => 'Email',
                'type'            => 'text',
                'entity_type'     => 'users',
                'lookup_type'     => NULL,
                'validation'      => NULL,
                'sort_order'      => '2',
                'is_required'     => '1',
                'is_unique'       => '1',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => '45',
                'code'            => 'password',
                'name'            => 'Password',
                'type'            => 'text',
                'entity_type'     => 'users',
                'lookup_type'     => NULL,
                'validation'      => NULL,
                'sort_order'      => '3',
                'is_required'     => '1',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ]
        ]);
    }
}