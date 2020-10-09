-- +migrate Up
alter table `users`
  modify `resetcode` varchar(100) CHARACTER SET utf8 NOT NULL default '',
  modify `resetcodetime` int(11) NOT NULL default 0,
  modify `pubcode` varchar(100) DEFAULT NULL default '',
  modify `authcode` varchar(100) CHARACTER SET utf8 NOT NULL default '';

-- +migrate Down
alter table `users`
  modify `resetcode` varchar(100) CHARACTER SET utf8 NOT NULL,
  modify `resetcodetime` int(11) NOT NULL,
  modify `pubcode` varchar(100) DEFAULT NULL,
  modify `authcode` varchar(100) CHARACTER SET utf8 NOT NULL;
