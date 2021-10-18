

CREATE TABLE `sh_order` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_name` varchar(255) DEFAULT NULL,
  `order_total` int(11) NOT NULL,
  `order_type` varchar(10) NOT NULL,
  `order_date` date NOT NULL,
  `order_deleted` int(1) NOT NULL DEFAULT 0,
  `created_at` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb4;



CREATE TABLE `sh_order_detail` (
  `detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `pro_id` int(11) NOT NULL,
  `detail_amount` int(11) NOT NULL,
  `detail_deteled` int(1) NOT NULL DEFAULT 0,
  `created_at` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`detail_id`)
) ENGINE=InnoDB AUTO_INCREMENT=144 DEFAULT CHARSET=utf8mb4;



CREATE TABLE `sh_product` (
  `pro_id` int(11) NOT NULL AUTO_INCREMENT,
  `pro_name` varchar(255) NOT NULL,
  `pro_image` varchar(255) NOT NULL,
  `pro_im_price` int(11) NOT NULL,
  `pro_ex_price` int(11) NOT NULL,
  `pro_amount` int(11) NOT NULL,
  `pro_amount_sell` int(11) NOT NULL DEFAULT 0,
  `pro_note` text DEFAULT NULL,
  `pro_type` int(11) NOT NULL,
  `pro_deleted` int(1) NOT NULL DEFAULT 0,
  `created_at` date DEFAULT NULL,
  PRIMARY KEY (`pro_id`),
  KEY `pro_type` (`pro_type`),
  CONSTRAINT `sh_product_ibfk_1` FOREIGN KEY (`pro_type`) REFERENCES `sh_product_type` (`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4;



CREATE TABLE `sh_product_type` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(255) NOT NULL,
  `type_deleted` int(1) NOT NULL DEFAULT 0,
  `created_at` date DEFAULT NULL,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4;



CREATE TABLE `sh_user` (
  `user_email` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_delete` int(1) NOT NULL DEFAULT 0,
  `created_at` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO sh_order (`order_id`, `order_name`, `order_total`, `order_type`, `order_date`, `order_deleted`, `created_at`) VALUES 
('93','','5000','Xuất','2021-10-16','0','');

INSERT INTO sh_order (`order_id`, `order_name`, `order_total`, `order_type`, `order_date`, `order_deleted`, `created_at`) VALUES 
('94','','0','Xuất','2021-10-16','0','');

INSERT INTO sh_order (`order_id`, `order_name`, `order_total`, `order_type`, `order_date`, `order_deleted`, `created_at`) VALUES 
('95','','100','Nhập','2021-10-18','0','');

INSERT INTO sh_order (`order_id`, `order_name`, `order_total`, `order_type`, `order_date`, `order_deleted`, `created_at`) VALUES 
('96','','400','Xuất','2021-10-18','0','');

INSERT INTO sh_order_detail (`detail_id`, `order_id`, `pro_id`, `detail_amount`, `detail_deteled`, `created_at`) VALUES 
('141','93','44','1','0','');

INSERT INTO sh_order_detail (`detail_id`, `order_id`, `pro_id`, `detail_amount`, `detail_deteled`, `created_at`) VALUES 
('142','95','40','1','0','');

INSERT INTO sh_order_detail (`detail_id`, `order_id`, `pro_id`, `detail_amount`, `detail_deteled`, `created_at`) VALUES 
('143','96','45','2','0','');

INSERT INTO sh_product (`pro_id`, `pro_name`, `pro_image`, `pro_im_price`, `pro_ex_price`, `pro_amount`, `pro_amount_sell`, `pro_note`, `pro_type`, `pro_deleted`, `created_at`) VALUES 
('25','SGK 11','storage/app/public/products/xkbB8kU4EGvCWaHM0N.png','120000','130000','99','111','','2','0','');

INSERT INTO sh_product (`pro_id`, `pro_name`, `pro_image`, `pro_im_price`, `pro_ex_price`, `pro_amount`, `pro_amount_sell`, `pro_note`, `pro_type`, `pro_deleted`, `created_at`) VALUES 
('26','Vỡ 5 ô ly','storage/app/public/products/kIrbg9Pwy4PAxFKiTD.png','12000','14000','99','21','','3','0','');

INSERT INTO sh_product (`pro_id`, `pro_name`, `pro_image`, `pro_im_price`, `pro_ex_price`, `pro_amount`, `pro_amount_sell`, `pro_note`, `pro_type`, `pro_deleted`, `created_at`) VALUES 
('27','bút bi','storage/app/public/products/pvzke4nzTyloTJ9KQ6.png','5000','6000','99','16','','1','0','');

INSERT INTO sh_product (`pro_id`, `pro_name`, `pro_image`, `pro_im_price`, `pro_ex_price`, `pro_amount`, `pro_amount_sell`, `pro_note`, `pro_type`, `pro_deleted`, `created_at`) VALUES 
('28','bút chì','storage/app/public/products/XQrNoBE9SZTCByFyHr.jpeg','3000','4000','91','23','','1','0','');

INSERT INTO sh_product (`pro_id`, `pro_name`, `pro_image`, `pro_im_price`, `pro_ex_price`, `pro_amount`, `pro_amount_sell`, `pro_note`, `pro_type`, `pro_deleted`, `created_at`) VALUES 
('40','tập','storage/app/public/products/qLFfqNUP9csRNzvznO.jpeg','100','100','1','1','','3','0','');

INSERT INTO sh_product (`pro_id`, `pro_name`, `pro_image`, `pro_im_price`, `pro_ex_price`, `pro_amount`, `pro_amount_sell`, `pro_note`, `pro_type`, `pro_deleted`, `created_at`) VALUES 
('45','tivir','storage/app/public/products/S5fvttiqG1bCVAR86P.gif','400','200','0','2','','2','0','');

INSERT INTO sh_product_type (`type_id`, `type_name`, `type_deleted`, `created_at`) VALUES 
('1','samsung2','0','');

INSERT INTO sh_product_type (`type_id`, `type_name`, `type_deleted`, `created_at`) VALUES 
('2','sách1','0','');

INSERT INTO sh_product_type (`type_id`, `type_name`, `type_deleted`, `created_at`) VALUES 
('3','samsung2a','0','');

INSERT INTO sh_product_type (`type_id`, `type_name`, `type_deleted`, `created_at`) VALUES 
('61','SFSD','1','');

INSERT INTO sh_product_type (`type_id`, `type_name`, `type_deleted`, `created_at`) VALUES 
('62','iiii','0','');

INSERT INTO sh_product_type (`type_id`, `type_name`, `type_deleted`, `created_at`) VALUES 
('63','ui','1','');

INSERT INTO sh_user (`user_email`, `user_password`, `user_name`, `user_delete`, `created_at`) VALUES 
('nguyentrungtin913@gmail.com','e10adc3949ba59abbe56e057f20f883e','Trung Tín','0','');
