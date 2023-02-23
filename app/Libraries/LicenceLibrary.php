<?php


namespace App\Libraries;

use App\Models\Licenca;
use App\Models\ZahtevLicenca;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * @author Marbo
 * Klasa za razne operacije sa licencama
 * Verzija 1.0, od 07.04.2021.god.
 * -------------------------------------
 * @modifyby Milan
 */
abstract class LicenceLibrary
{


    /**
     * @param string $jmbg
     * @return Collection|null
     * Getting collection of licences that osoba owns with statuses 'A', or 'N'
     */
    public static function get(string $jmbg): ?Collection
    {
        return Licenca::where('osoba', $jmbg)->whereIn('status', ['A', 'N'])->get();
    }

    /**
     * @param string $licenca
     * @return bool
     * Method checks existence of licence with status 'A' or 'N'
     * This method also should be used to check if licence is present in Registar
     */
    public static function exists(string $licenca): bool
    {
        return Licenca::find($licenca)->whereIn('status', ['A', 'N'])->isNotEmpty();
    }

    /**
     * @param \App\Models\Request $request
     * @param string $datum_dokumenta
     * @param string $broj_dokumenta
     * @throws \Exception
     */
    public static function deactivate(
        \App\Models\Request $request,
        string $datum_dokumenta,
        string $broj_dokumenta
    ): void
    {

        // getting persons licences
        $licence = self::get($request->osoba_id);

        if ($licence->isEmpty())
            throw new \Exception("Nema evidentiranih licenci u bazi.");

        // get uzrok by request category
        $uzrok = self::getUzrokDeaktivacije($request);

        $datum_dokumenta_string = Carbon::parse($datum_dokumenta)->format('d.m.Y.');

        // updating licenca model
        foreach ($licence as $licenca) {

            $licenca->status = 'D';
            $licenca->datumukidanja = $datum_dokumenta;
            if (empty($licenca->razlogukidanja)) {
                $licenca->razlogukidanja = "Licenca deaktivirana na osnovu Rešenja o brisanju podataka upisanih u Registar broj $broj_dokumenta od $datum_dokumenta_string godine $uzrok.";
            } else {
                $licenca->razlogukidanja = "$licenca->razlogukidanja##Licenca deaktivirana na osnovu Rešenja o brisanju podataka upisanih u Registar broj $broj_dokumenta od $datum_dokumenta_string godine $uzrok.";
            }


            if (!$licenca->save())
                throw new \Exception("Greška prilikom ažuriranja licence u bazi.");
        }

    }

    /**
     * @param \App\Models\Request $request
     * @return string
     */
    private static function getUzrokDeaktivacije(\App\Models\Request $request): string
    {
        $usled_smrti = 11;
        $na_licni_zahtev = 14;

        switch ($request->request_category_id) {
            case $usled_smrti:
                $uzrok = 'Usled smrti.';
                break;
            case $na_licni_zahtev:
                $uzrok = 'Na lični zahtev.';
                break;
        }

        return $uzrok;
    }


    public function proveriStatusLicence(Request $request)
    {
//
    }


    /**
     * @param ZahtevLicenca $zahtev
     * @return bool
     */
    public function kreirajlicencu(ZahtevLicenca $zahtev)
    {
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
            return TRUE;
        } else {
            return FALSE;
        }
    }


}
