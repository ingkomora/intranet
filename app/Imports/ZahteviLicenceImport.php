<?php

namespace App\Imports;

use App\Models\Osoba;
use App\Models\ZahtevLicenca;
use App\Models\Licenca;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;


class ZahteviLicenceImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure {
    use Importable, SkipsErrors, SkipsFailures;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row) {
        $row = array_map('trim', $row);
//        dd($row['licenca']);

        $osoba = Osoba::find($row['jmbg']);
        $zahtev = ZahtevLicenca::find($row['jmbg']);
        $licenca = Licenca::find($row['licenca']);
        if (!is_null($licenca) AND !is_null($osoba) ) {

//        dd($licenca->osoba);
            return new ZahtevLicenca([
                'osoba' => $row['jmbg'],
                'licencatip' => $row['licencatip'],
                'licenca_broj' => $row['licenca_broj'],
                'reg_pod_oblast_id' => $row['reg_pod_oblast_id'],
                'vrsta_posla_id' => $row['vrsta_posla_id'],
                'licenca_datum_resenja' => $row['licenca_datum_resenja'],
                'licenca_broj_resenja' => $row['licenca_broj_resenja'],
                'datum' => date("Y-m-d"),
//            'prijava_clan_id' => trim($row['prijava_clan_id']),
                'status' => $row['status']
            ]);
        }
    }

    /*    public function onError(\Throwable $e)
        {
            info("Greska: $e");

            // Handle the exception how you'd like.
        }*/

    public function rules(): array {
        return [
            'import' => Rule::notIn([0]),
//            'licenca_broj' => Rule::unique('tzahtev')->where(function ($query) {
//                return $query->whereNotNull('licenca_broj');
//            })
        ];
    }

    /**
     * @param Failure[] $failures
     */
    public function onFailure(Failure ...$failures) {
//        dd($failures);
        info("ZahteviImport Greska u validaciji");
        // Handle the failures how you'd like.
    }

    public function onError(\Throwable $e) {
        info("ZahteviImport Greska: $e");

        // Handle the exception how you'd like.
    }

}
