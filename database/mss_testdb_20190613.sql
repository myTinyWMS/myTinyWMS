drop database if exists mss_test;
create schema mss_test collate latin1_swedish_ci;

use mss_test;

create table action_events
(
	id bigint unsigned auto_increment
		primary key,
	batch_id char(36) not null,
	user_id int unsigned not null,
	name varchar(191) not null,
	actionable_type varchar(191) not null,
	actionable_id int unsigned not null,
	target_type varchar(191) not null,
	target_id int unsigned not null,
	model_type varchar(191) not null,
	model_id int unsigned null,
	fields text not null,
	status varchar(25) default 'running' not null,
	exception text not null,
	created_at timestamp null,
	updated_at timestamp null
)
collate=utf8mb4_unicode_ci;

create index action_events_actionable_type_actionable_id_index
	on action_events (actionable_type, actionable_id);

create index action_events_batch_id_model_type_model_id_index
	on action_events (batch_id, model_type, model_id);

create index action_events_user_id_index
	on action_events (user_id);

create table article_notes
(
	id int unsigned auto_increment
		primary key,
	article_id int unsigned not null,
	user_id int unsigned not null,
	content text not null,
	created_at timestamp null,
	updated_at timestamp null
)
collate=utf8mb4_unicode_ci;

create table article_quantity_changelogs
(
	id int unsigned auto_increment
		primary key,
	article_id int unsigned not null,
	user_id int unsigned not null,
	type tinyint unsigned not null,
	`change` int not null,
	new_quantity int not null,
	note varchar(191) null,
	created_at timestamp null,
	updated_at timestamp null,
	delivery_item_id int unsigned null,
	unit_id int unsigned null,
	related_id int unsigned null
)
collate=utf8mb4_unicode_ci;

create index article_id_index
	on article_quantity_changelogs (article_id);

create table article_supplier
(
	id int unsigned auto_increment
		primary key,
	article_id int unsigned not null,
	supplier_id int unsigned not null,
	order_number varchar(191) not null,
	price int not null,
	delivery_time varchar(191) null,
	order_quantity int null,
	created_at timestamp null,
	updated_at timestamp null
)
collate=utf8mb4_unicode_ci;

create index article_supplier_article_id_index
	on article_supplier (article_id);

create table articles
(
	id int unsigned auto_increment
		primary key,
	name text not null,
	article_number varchar(191) null,
	unit_id int unsigned null,
	category_id int unsigned null,
	status int default 0 not null,
	quantity int not null,
	min_quantity int default 0 not null,
	usage_quantity int default 1 not null,
	issue_quantity int default 1 not null,
	sort_id int default 0 not null,
	inventory tinyint(1) default 1 not null,
	notes text null,
	created_at timestamp null,
	updated_at timestamp null,
	deleted_at timestamp null,
	order_notes text null,
	files text null,
	free_lines_in_printed_list int default 1 not null,
	outsourcing_quantity int default 0 not null,
	replacement_delivery_quantity int default 0 not null,
	cost_center varchar(191) null,
	weight int null,
	packaging_category varchar(191) null
)
collate=utf8mb4_unicode_ci;

create table audits
(
	id int unsigned auto_increment
		primary key,
	user_id int unsigned null,
	event varchar(191) not null,
	auditable_type varchar(191) not null,
	auditable_id bigint unsigned not null,
	old_values text null,
	new_values text null,
	url text null,
	ip_address varchar(45) null,
	user_agent varchar(191) null,
	tags varchar(191) null,
	created_at timestamp null,
	updated_at timestamp null,
	user_type varchar(191) null
)
collate=utf8mb4_unicode_ci;

create index audits_auditable_type_auditable_id_index
	on audits (auditable_type, auditable_id);

create table categories
(
	id int unsigned auto_increment
		primary key,
	name varchar(191) not null,
	notes text null,
	created_at timestamp null,
	updated_at timestamp null,
	deleted_at timestamp null
)
collate=utf8mb4_unicode_ci;

create table deliveries
(
	id int unsigned auto_increment
		primary key,
	order_id int unsigned not null,
	delivery_date date not null,
	delivery_note_number varchar(191) null,
	notes text null,
	created_at timestamp null,
	updated_at timestamp null
)
collate=utf8mb4_unicode_ci;

create table delivery_items
(
	id int unsigned auto_increment
		primary key,
	delivery_id int unsigned not null,
	article_id int unsigned not null,
	quantity int not null,
	created_at timestamp null,
	updated_at timestamp null
)
collate=utf8mb4_unicode_ci;

create table failed_jobs
(
	id bigint unsigned auto_increment
		primary key,
	connection text not null,
	queue text not null,
	payload longtext not null,
	exception longtext not null,
	failed_at timestamp default current_timestamp() not null
)
collate=utf8mb4_unicode_ci;

create table inventories
(
	id int unsigned auto_increment
		primary key,
	created_at timestamp null,
	updated_at timestamp null,
	started_by int not null
)
collate=utf8mb4_unicode_ci;

create table inventory_items
(
	id int unsigned auto_increment
		primary key,
	created_at timestamp null,
	updated_at timestamp null,
	inventory_id int not null,
	article_id int not null,
	processed_by int null,
	processed_at datetime null,
	old_quantity int null,
	new_quantity int null
)
collate=utf8mb4_unicode_ci;

create table jobs
(
	id bigint unsigned auto_increment
		primary key,
	queue varchar(191) not null,
	payload longtext not null,
	attempts tinyint unsigned not null,
	reserved_at int unsigned null,
	available_at int unsigned not null,
	created_at int unsigned not null
)
collate=utf8mb4_unicode_ci;

create index jobs_queue_index
	on jobs (queue);

create table migrations
(
	id int unsigned auto_increment
		primary key,
	migration varchar(191) not null,
	batch int not null
)
collate=utf8mb4_unicode_ci;

create table notifications
(
	id char(36) not null
		primary key,
	type varchar(191) not null,
	notifiable_type varchar(191) not null,
	notifiable_id bigint unsigned not null,
	data text not null,
	read_at timestamp null,
	created_at timestamp null,
	updated_at timestamp null
)
collate=utf8mb4_unicode_ci;

create index notifications_notifiable_type_notifiable_id_index
	on notifications (notifiable_type, notifiable_id);

create table order_items
(
	id int unsigned auto_increment
		primary key,
	created_at timestamp null,
	updated_at timestamp null,
	order_id int unsigned null,
	article_id int unsigned not null,
	price int unsigned not null,
	quantity int unsigned not null,
	expected_delivery date null,
	confirmation_received tinyint(1) default 0 not null,
	invoice_received tinyint(1) default 0 not null
)
collate=utf8mb4_unicode_ci;

create table order_messages
(
	id int unsigned auto_increment
		primary key,
	order_id int unsigned null,
	user_id int unsigned null,
	received datetime not null,
	sender varchar(191) not null,
	receiver varchar(191) not null,
	subject text not null,
	htmlBody mediumtext null,
	textBody mediumtext null,
	attachments text null,
	`read` tinyint(1) default 0 not null,
	created_at timestamp null,
	updated_at timestamp null,
	deleted_at timestamp null
)
collate=utf8mb4_unicode_ci;

create table orders
(
	id int unsigned auto_increment
		primary key,
	created_at timestamp null,
	updated_at timestamp null,
	internal_order_number varchar(191) not null,
	external_order_number varchar(191) null,
	status tinyint unsigned default 0 not null,
	supplier_id int unsigned null,
	total_cost int unsigned default 0 not null,
	shipping_cost int unsigned default 0 not null,
	expected_delivery date null,
	order_date date null,
	notes text null,
	confirmation_received tinyint(1) default 0 not null,
	invoice_received tinyint(1) default 0 not null,
	payment_status tinyint default 0 null
)
collate=utf8mb4_unicode_ci;

create table password_resets
(
	email varchar(191) not null,
	token varchar(191) not null,
	created_at timestamp null
)
collate=utf8mb4_unicode_ci;

create index password_resets_email_index
	on password_resets (email);

create table suppliers
(
	id int unsigned auto_increment
		primary key,
	name varchar(191) not null,
	email varchar(191) null,
	phone varchar(191) null,
	contact_person varchar(191) null,
	website varchar(191) null,
	notes text null,
	created_at timestamp null,
	updated_at timestamp null,
	deleted_at timestamp null
)
collate=utf8mb4_unicode_ci;

create table taggables
(
	tag_id int unsigned not null,
	taggable_type varchar(191) not null,
	taggable_id bigint unsigned not null
)
collate=utf8mb4_unicode_ci;

create index taggables_taggable_type_taggable_id_index
	on taggables (taggable_type, taggable_id);

create table tags
(
	id int unsigned auto_increment
		primary key,
	name varchar(191) not null
)
collate=utf8mb4_unicode_ci;

create table units
(
	id int unsigned auto_increment
		primary key,
	name varchar(191) not null,
	created_at timestamp null,
	updated_at timestamp null
)
collate=utf8mb4_unicode_ci;

create table users
(
	id int unsigned auto_increment
		primary key,
	name varchar(191) not null,
	email varchar(191) not null,
	password varchar(191) not null,
	remember_token varchar(100) null,
	created_at timestamp null,
	updated_at timestamp null,
	settings longtext default '[]' null comment '(DC2Type:json)',
	signature text null,
	username varchar(191) null,
	constraint users_email_unique
		unique (email)
)
collate=utf8mb4_unicode_ci;


INSERT INTO mss_test.migrations (id, migration, batch) VALUES (415, '2014_10_12_000000_create_users_table', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (416, '2014_10_12_100000_create_password_resets_table', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (417, '2018_01_01_000000_create_action_events_table', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (418, '2018_01_10_213109_create_notifications_table', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (419, '2018_01_12_185950_create_articles_table', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (420, '2018_01_12_190201_create_suppliers_table', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (421, '2018_01_12_190316_create_article_supplier_table', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (422, '2018_01_12_190445_create_categories_table', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (423, '2018_01_12_190721_create_units_table', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (424, '2018_01_13_153558_create_audits_table', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (425, '2018_01_24_200925_create_article_notes_table', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (426, '2018_01_27_213424_create_tags_table', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (427, '2018_01_28_204219_create_article_quantity_changelogs_table', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (428, '2018_02_03_205331_create_orders_table', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (429, '2018_02_03_205351_create_order_items_table', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (430, '2018_02_12_215411_create_deliveries_table', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (431, '2018_02_12_215757_create_delivery_items_table', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (432, '2018_03_06_205458_add_confirmation_received_to_order', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (433, '2018_03_06_223658_create_order_messages_table', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (434, '2018_03_11_210000_add_invoice_received_to_order', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (435, '2018_03_13_213105_add_soft_deletes_to_order_messages', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (436, '2018_04_11_220911_add_delivery_item_id_to_article_quantity_changelog', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (437, '2018_04_18_204502_add_payment_status_to_orders', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (438, '2018_04_23_090659_add_unit_to_article_quantity_changelog', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (439, '2018_04_24_164551_add_order_notes_to_article', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (440, '2018_04_24_201648_add_more_details_to_order_item', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (441, '2018_04_26_210823_add_settings_to_user', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (442, '2018_05_08_112251_add_article_id_index_to_article_quantity_changelog', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (443, '2018_05_21_212420_add_files_to_articles', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (444, '2018_06_14_210746_add_signature_to_user', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (445, '2018_06_22_160205_resize_content_columns_in_order_messages', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (446, '2018_06_26_213133_add_username_to_users', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (447, '2018_06_26_214637_create_jobs_table', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (448, '2018_07_26_145001_create_inventories_table', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (449, '2018_07_26_145014_create_inventory_items_table', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (450, '2018_07_27_155117_add_free_lines_in_printed_list', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (451, '2018_08_28_211538_add_related_id_to_article_quantity_changelog', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (452, '2018_10_04_121550_add_extra_quantities_to_articles', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (453, '2018_10_04_164659_add_default_to_user_settings', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (454, '2018_11_02_141517_added_cost_center_to_articles', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (455, '2018_11_23_152205_update_audits_table', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (456, '2018_11_26_085414_create_failed_jobs_table', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (457, '2018_11_27_102032_add_weight_to_article', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (458, '2018_12_17_115652_add_packaging_category_to_article', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (459, '2018_12_19_105935_add_quantities_to_inventory_item', 1);
INSERT INTO mss_test.migrations (id, migration, batch) VALUES (460, '2018_12_20_101046_add_index_to_article_supplier', 1);
