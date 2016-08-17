SET SQL_SAFE_UPDATES = 0;

DELETE FROM _models WHERE table_from IN ('_changes', '_crypt', '_drafts', '_history', '_log', '_sessions', '_token');
TRUNCATE `_changes`;
TRUNCATE `_crypt`;
TRUNCATE `_drafts`;
TRUNCATE `_history`;
TRUNCATE `_log`;
TRUNCATE `_sessions`;
TRUNCATE `_token`;

DELETE FROM _models WHERE table_from IN ('e_campaign', 'e_cron', 'e_delivered', 'e_request', 'e_stat', 'e_subscriber');
TRUNCATE `e_campaign`;
TRUNCATE `e_cron`;
TRUNCATE `e_delivered`;
TRUNCATE `e_request`;
TRUNCATE `e_stat`;
TRUNCATE `e_subscriber`;

DELETE FROM _models WHERE table_from IN ('media', 'message', 'post', 'pricing', 'product', 'referencia', 'room', 'slide', 'tag', 'testimonial', 'menu', 'freecontent','booking');
TRUNCATE `booking`;
TRUNCATE `freecontent`;
TRUNCATE `media`;
TRUNCATE `menu`;
TRUNCATE `message`;
TRUNCATE `post`;
TRUNCATE `pricing`;
TRUNCATE `product`;
TRUNCATE `referencia`;
TRUNCATE `room`;
TRUNCATE `slide`;
TRUNCATE `tag`;
TRUNCATE `testimonial`;

DELETE FROM member WHERE id NOT IN (1,2,3);
DELETE FROM _models WHERE model_class = 'member' AND table_id NOT IN (1,2,3);

ALTER TABLE `_models` AUTO_INCREMENT = 100;

DELETE FROM _model_metas WHERE reference NOT IN (SELECT reference FROM _models);
ALTER TABLE `_model_metas` AUTO_INCREMENT = 100;
DELETE FROM _model_orders WHERE reference NOT IN (SELECT reference FROM _models);
ALTER TABLE `_model_orders` AUTO_INCREMENT = 100;
DELETE FROM _model_states WHERE model NOT IN (SELECT reference FROM _models);
ALTER TABLE `_model_states` AUTO_INCREMENT = 100;
DELETE FROM _model_trees WHERE reference NOT IN (SELECT reference FROM _models);
ALTER TABLE `_model_trees` AUTO_INCREMENT = 100;
DELETE FROM _permissions WHERE reference NOT IN (SELECT reference FROM _models);
ALTER TABLE `_permissions` AUTO_INCREMENT = 100;
DELETE FROM _rewrites WHERE reference NOT IN (SELECT reference FROM _models);
ALTER TABLE `_rewrites` AUTO_INCREMENT = 100;

SET SQL_SAFE_UPDATES = 1;