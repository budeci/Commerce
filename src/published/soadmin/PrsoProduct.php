<?php

Admin::model('Commerce\Productso\Models\PrsoProduct')->title('Products')->display(function ()
{
	$display = AdminDisplay::datatables();
	$display->with();
	$display->filters([

	]);
	$display->columns([
		Column::string('name')->label('Name'),
		Column::string('id')->label('Id'),
		Column::string('show')->label('Show'),
		Column::string('views')->label('Views'),
		Column::datetime('created_at')->label('Created')->format('d.m.Y'),
	]);
	return $display;
})->createAndEdit(function ()
{
	$form = AdminForm::form();
	$form->items([
//		FormItem::text('category_id', 'Category'),
		FormItem::text('name', 'Name')->required(),
		FormItem::text('cost', 'Price'),
		FormItem::text('slug', 'Slug'),
		FormItem::multiselect('categories', 'Categories')->model('Commerce\Productso\Models\PrsoCategory')->display('name'),
		FormItem::text('views', 'Views')->readonly(),
		FormItem::checkbox('show', 'Show')->defaultValue(true),
//		FormItem::checkbox('complected', 'Complected'),
//		FormItem::text('complect_id', 'Complect'),
		FormItem::ckeditor('note', 'Note'),
		FormItem::ckeditor('description', 'Description'),
		FormItem::multiimages('photos', 'Images'),
	]);
	return $form;
});