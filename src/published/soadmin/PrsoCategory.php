<?php

Admin::model(Commerce\Productso\Models\PrsoCategory::class)->title('Categories')->display(function ()
{
	$display = AdminDisplay::tree();
	$display->value('name');
	return $display;

})->createAndEdit(function ()
{
	$form = AdminForm::form();
	$form->items([
		FormItem::text('name', 'Name'),
		FormItem::text('slug', 'Slug'),
		FormItem::ckeditor('note', 'Note'),
		FormItem::ckeditor('desc', 'Description'),
	]);
	return $form;
});