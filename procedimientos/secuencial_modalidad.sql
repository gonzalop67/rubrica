DELIMITER $$
CREATE FUNCTION `secuencial_modalidad`() RETURNS int(11)
    NO SQL
BEGIN
	DECLARE Secuencial INT;
	
	SET Secuencial = (
		SELECT MAX(mo_orden)
		  FROM sw_modalidad);
           
    SET Secuencial = IFNULL(Secuencial, 0);

	RETURN Secuencial + 1;
END$$
DELIMITER ;