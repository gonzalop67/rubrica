<?php
class Menu
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function listarMenusNivel1($id_perfil)
    {
        $this->db->query("SELECT m.*
                            FROM sw_menu m,
                                 sw_menu_perfil mp 
                           WHERE m.id_menu = mp.id_menu
                             AND mp.id_perfil = $id_perfil 
                             AND mnu_padre = 0
                             AND mnu_publicado = 1
                        ORDER BY mnu_orden");

        return $this->db->registros();
    }

    public function listarMenusHijos($mnu_padre)
    {
        $this->db->query("SELECT *
                            FROM sw_menu
                           WHERE mnu_padre = $mnu_padre
                             AND mnu_publicado = 1
                        ORDER BY mnu_orden");

        return $this->db->registros();
    }

    public function obtenerMenusPadres()
    {
        $this->db->query("SELECT m.*, pe_nombre FROM `sw_menu` m, `sw_perfil` p, `sw_menu_perfil` mp  WHERE p.id_perfil = mp.id_perfil AND m.id_menu = mp.id_menu AND mnu_padre = 0 ORDER BY pe_nombre, mnu_padre, mnu_orden");

        return $this->db->registros();
    }

    public function obtenerMenusHijos($mnu_padre)
    {
        $this->db->query("SELECT *
                            FROM sw_menu
                           WHERE mnu_padre = $mnu_padre
                        ORDER BY mnu_orden");

        return $this->db->registros();
    }

    public function insertarMenu($datos)
    {
        $this->db->query('INSERT INTO sw_menu (mnu_texto, mnu_link, mnu_publicado, mnu_icono, id_perfil) VALUES (:mnu_texto, :mnu_link, :mnu_publicado, :mnu_icono, :id_perfil)');

        //Vincular valores
        $this->db->bind(':mnu_texto', $datos['mnu_texto']);
        $this->db->bind(':mnu_link', $datos['mnu_link']);
        $this->db->bind(':mnu_publicado', $datos['mnu_publicado']);
        $this->db->bind(':mnu_icono', $datos['mnu_icono']);
        $this->db->bind(':id_perfil', $datos['id_perfil']);

        $this->db->execute();

        $this->db->query("SELECT MAX(id_menu) AS lastInsertId FROM sw_menu");
        $lastInsertId = $this->db->registro()->lastInsertId;

        $this->db->query('INSERT INTO sw_menu_perfil (id_perfil, id_menu) VALUES (:id_perfil, :id_menu)');

        //Vincular valores
        $this->db->bind(':id_perfil', $datos['id_perfil']);
        $this->db->bind(':id_menu', $lastInsertId);

        $this->db->execute();
    }

    public function obtenerMenuPorId($id)
    {
        $this->db->query("SELECT m.*, mp.id_perfil FROM `sw_menu` m, `sw_menu_perfil` mp WHERE m.id_menu = mp.id_menu AND m.id_menu = :id_menu");
        $this->db->bind(':id_menu', $id);

        return $this->db->registro();
    }

    public function actualizarMenu($datos)
    {
        $this->db->query('UPDATE sw_menu SET mnu_texto = :mnu_texto, mnu_link = :mnu_link, mnu_publicado = :mnu_publicado, mnu_icono = :mnu_icono WHERE id_menu = :id_menu');

        //Vincular valores
        $this->db->bind(':mnu_texto', $datos['mnu_texto']);
        $this->db->bind(':mnu_link', $datos['mnu_link']);
        $this->db->bind(':mnu_publicado', $datos['mnu_publicado']);
        $this->db->bind(':mnu_icono', $datos['mnu_icono']);
        $this->db->bind(':id_menu', $datos['id_menu']);

        $this->db->execute();

        $this->db->query('UPDATE sw_menu_perfil SET id_perfil = :id_perfil WHERE id_menu = :id_menu');

        //Vincular valores
        $this->db->bind(':id_perfil', $datos['id_perfil']);
        $this->db->bind(':id_menu', $datos['id_menu']);

        $this->db->execute();
    }

    public function eliminarMenu($id)
    {
        $this->listarMenusHijos($id);

        if ($this->db->rowCount() == 0) {
            $this->db->query("DELETE FROM sw_menu WHERE id_menu = :id_menu");

            //Vincular valores
            $this->db->bind(':id_menu', $id);

            $this->db->execute();
        }
    }

    public function guardarOrden($menus)
    {
        foreach ($menus as $var => $value) {
            // $this->where('id', $value->id)->update(['menu_id' => 0, 'orden' => $var + 1]);
            $this->db->query('UPDATE sw_menu SET mnu_padre = :mnu_padre, mnu_orden = :mnu_orden, mnu_nivel = :mnu_nivel WHERE id_menu = :id_menu');
            //Vincular valores
            $this->db->bind(':mnu_padre', 0);
            $this->db->bind(':mnu_nivel', 1);
            $this->db->bind(':mnu_orden', $var + 1);
            $this->db->bind(':id_menu', $value['id']);
            //Ejecutar la sentencia UPDATE
            $this->db->execute();
            if (!empty($value['children'])) {
                foreach ($value['children'] as $key => $vchild) {
                    $update_id = $vchild['id'];
                    $parent_id = $value['id'];
                    // $this->where('id', $update_id)->update(['menu_id' => $parent_id, 'orden' => $key + 1]);
                    $this->db->query('UPDATE sw_menu SET mnu_padre = :mnu_padre, mnu_orden = :mnu_orden, mnu_nivel = :mnu_nivel WHERE id_menu = :id_menu');
                    //Vincular valores
                    $this->db->bind(':mnu_padre', $parent_id);
                    $this->db->bind(':mnu_nivel', 2);
                    $this->db->bind(':mnu_orden', $key + 1);
                    $this->db->bind(':id_menu', $update_id);
                    //Ejecutar la sentencia UPDATE
                    $this->db->execute();
                }
            }
        }
        return count($menus);
    }
}
