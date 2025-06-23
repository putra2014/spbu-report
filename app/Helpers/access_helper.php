<?php

function hasAccessToSPBU($targetKodeSpbu)
{
    $session = session();
    $role = $session->get('role');
    $userKodeSpbu = $session->get('kode_spbu');

    if ($role === 'admin_region') {
        return true;
    }

    if ($role === 'admin_area') {
        $area_id = $session->get('area_id');
        $db = \Config\Database::connect();
        $spbu = $db->table('spbu')->where('kode_spbu', $targetKodeSpbu)->get()->getRow();
        return $spbu && $spbu->area_id == $area_id;
    }

    if ($role === 'admin_spbu') {
        return $userKodeSpbu == $targetKodeSpbu;
    }

    return false;
}

