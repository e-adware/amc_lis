UPDATE `menu_header_master` SET `name` = 'Reagement Management' WHERE `menu_header_master`.`id` = 15;
INSERT INTO `menu_master` (`par_id`, `par_name`, `header`, `sequence`, `hidden`) VALUES ('271', 'Item Master', '15', '1', '0');
INSERT INTO `menu_master` (`par_id`, `par_name`, `header`, `sequence`, `hidden`) VALUES ('272', 'Instrument Master', '15', '2', '0');
INSERT INTO `menu_master` (`par_id`, `par_name`, `header`, `sequence`, `hidden`) VALUES ('273', 'Packing Master', '15', '3', '0');
INSERT INTO `menu_master` (`par_id`, `par_name`, `header`, `sequence`, `hidden`) VALUES ('274', 'Supplier Master', '15', '4', '0');
INSERT INTO `menu_master` (`par_id`, `par_name`, `header`, `sequence`, `hidden`) VALUES ('275', 'Purchase Entry', '15', '5', '0');
INSERT INTO `menu_master` (`par_id`, `par_name`, `header`, `sequence`, `hidden`) VALUES ('276', 'Purchase Report', '15', '6', '0');
INSERT INTO `menu_master` (`par_id`, `par_name`, `header`, `sequence`, `hidden`) VALUES ('277', 'Stock Report', '15', '7', '0');
INSERT INTO `menu_master` (`par_id`, `par_name`, `header`, `sequence`, `hidden`) VALUES ('278', 'Indent Order', '15', '8', '0');
INSERT INTO `menu_master` (`par_id`, `par_name`, `header`, `sequence`, `hidden`) VALUES ('279', 'Indent Order Details', '15', '9', '0');
INSERT INTO `menu_master` (`par_id`, `par_name`, `header`, `sequence`, `hidden`) VALUES ('280', 'Indent Issue', '15', '10', '0');
INSERT INTO `menu_master` (`par_id`, `par_name`, `header`, `sequence`, `hidden`) VALUES ('282', 'Indent Issue Report', '15', '11', '0');
INSERT INTO `menu_master` (`par_id`, `par_name`, `header`, `sequence`, `hidden`) VALUES ('283', 'Lab Stock Report', '15', '12', '0');
INSERT INTO `menu_master` (`par_id`, `par_name`, `header`, `sequence`, `hidden`) VALUES ('284', 'Lab Stock Consume Report', '15', '13', '0');
INSERT INTO `menu_master` (`par_id`, `par_name`, `header`, `sequence`, `hidden`) VALUES ('285', 'Reagent Consumption Details', '15', '14', '0'); 
INSERT INTO `menu_master` (`par_id`, `par_name`, `header`, `sequence`, `hidden`) VALUES ('286', 'Reagent Consumption Machine Report', '15', '15', '0');

INSERT INTO `menu_access_detail` (`levelid`, `par_id`) VALUES ('1', '271'), ('1', '272'), ('1', '273'), ('1', '274'), ('1', '275'), ('1', '276'), ('1', '277'), ('1', '278'), ('1', '279'), ('1', '280'), ('1', '282'), ('1', '283'), ('1', '284'), ('1', '285'), ('1', '286'); 
