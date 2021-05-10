<?php


namespace App\Libraries;

use App\Models\Licenca;
use App\Models\ZahtevLicenca;
use Illuminate\Http\Request;

/**
 * @author Marbo
 * Klasa za razne operacije sa licencama
 * Verzija 1.0, od 07.04.2021.god.
 */
class LicenceLibrary {
    public function proveriStatusLicence(Request $request) {
//
    }


    /**
     * @param ZahtevLicenca $zahtev
     * @return bool
     */
    public function kreirajlicencu(ZahtevLicenca $zahtev) {
        $status_licenca = NULL;
        $provera = new ProveraLibrary();
        if ($provera->statusNoveLicence($zahtev)) {
            $status_licenca = LICENCA_AKTIVNA;
        } else {
            $status_licenca = LICENCA_NEAKTIVNA;
        }
        $zahtev->status = ZAHTEV_LICENCA_ZAVRSEN;
        if (empty($zahtev->prijavaClan)) {
            $datum_prijema = now()->format("Y-m-d");
            $statusGrupa = LICENCE;
        } else {
            $datum_prijema = $zahtev->prijavaClan->datum_prijema;
            $statusGrupa = CLANSTVO;
        }
        if (empty($zahtev->prijem)) {
            $zahtev->prijem = $datum_prijema;
            $zahtev->save();
        }
        $this->log($zahtev, $statusGrupa, "Ažuriran zahtev: $zahtev->id, status: " . ZAHTEV_LICENCA_ZAVRSEN);
        $zahtev->licenca_broj = strtoupper(trim($zahtev->licenca_broj));
        $licenca = Licenca::firstOrNew(['id' => $zahtev->licenca_broj]);
        $licenca->id = $zahtev->licenca_broj;
        $licenca->licencatip = $zahtev->licencatip;
        $licenca->osoba = $zahtev->osoba;
        $licenca->datum = $zahtev->datum;
        $licenca->zahtev = $zahtev->id;
        $licenca->datumuo = $zahtev->licenca_datum_resenja;
        $licenca->datumobjave = $zahtev->licenca_datum_resenja;
        $licenca->status = $status_licenca;
        $licenca->preuzeta = 1;
        if (Licenca::where('osoba', $zahtev->osoba)->where('prva', 1)->get()->isEmpty()) {
            $licenca->prva = 1;
            // default je 0 ako nije prva
        }
        $licenca->broj_resenja = $zahtev['licenca_broj_resenja'];
        if ($licenca->exists) {
            // licenca already exists
            $licenca->update();
            $this->logOsoba($licenca, $statusGrupa, "Ažurirana licenca: $licenca->id ($zahtev->osoba), status: $licenca->status");
        } else {
            // licenca created from 'new'; does not exist in database.
            $licenca->save();
            $this->logOsoba($licenca, $statusGrupa, "Kreirana licenca: $licenca->id ($zahtev->osoba), status: $licenca->status");
        }
//        TODO implementirati proveru snimanja i vratiti false
        if ($licenca->exists) {
            return true;
        } else {
            return false;
        }
    }


}