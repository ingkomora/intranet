<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PromenaPodatakaEmailRequest;
use App\Models\Licenca;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PrijavaPromenaPodatakaCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PromenaPodatakaCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;

//    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

//    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use Operations\PromenaPodatakaEmailOperation;
    use Operations\PromenaPodatakaObradaBulkOperation;

    protected
        $columns_definition_array = [
        'id',
        'licni_podaci' => [
            'label' => 'LIČNI PODACI',
            'name' => 'licni_podaci',
        ],
        'osoba' => [
            'name' => 'osoba',
//            'name' => 'licenca',
//            'name' => 'licenca.osobaId',
            'type' => 'select',
//            'type' => 'relationship',
            'label' => 'Ime prezime (jmbg)',
            'entity' => 'licenca',
            'attribute' => 'ime_prezime_jmbg',
//            'attribute' => 'ime_prezime_licence',
            'model' => 'App\Models\Licenca',
        ], // virtual column
        /*'ime'=>[
            'name'=> 'ime'
        ],
        'prezime'=>[
            'name'=> 'prezime'
        ],*/
        'brlic' => [
            'name' => 'brlic',
            'label' => 'Broj licence'
        ],
        'adresa' => [
            'name' => 'adresa',
            'label' => 'Adresa prebivališta'
        ],
        'mesto' => [
            'name' => 'mesto',
            'label' => 'Mesto prebivališta'
        ],
        'pbroj' => [
            'name' => 'pbroj',
            'label' => 'Poštanski broj mesta prebivališta'
        ],
//        'topstina_id',
        'opstina' => [
            'name' => 'opstina',
            'type' => 'relationship',
            'attribute' => 'ime',
            'ajax' => TRUE,
            'label' => 'Opština',
        ],
        'tel' => [
            'name' => 'tel',
            'label' => 'Telefon'
        ],
        'mob' => [
            'name' => 'mob',
            'label' => 'Mobilni'
        ],
        'email',
        'firma_podaci' => [
            'label' => 'PODACI O FIRMI',
            'name' => 'firma_podaci',
        ],
        'nazivfirm' => [
            'name' => 'nazivfirm',
            'label' => 'Naziv'
        ],
        'adresafirm' => [
            'name' => 'adresafirm',
            'label' => 'Adresa'
        ],
        'mestofirm' => [
            'name' => 'mestofirm',
            'label' => 'Mesto'
        ],
        'opstinafirm' => [
            'name' => 'opstinafirm',
            'label' => 'Opština'
        ],
        'mbfirm' => [
            'name' => 'mbfirm',
            'label' => 'Matični broj'
        ],
        'pibfirm' => [
            'name' => 'pibfirm',
            'label' => 'PIB'
        ],
        'emailfirm' => [
            'name' => 'emailfirm',
            'label' => 'Email'
        ],
        'telfirm' => [
            'name' => 'telfirm',
            'label' => 'Telefon'
        ],
        'wwwfirm' => [
            'name' => 'wwwfirm',
            'label' => 'www'
        ],
        'zahtev_podaci' => [
            'label' => 'PODACI O ZAHTEVU',
            'name' => 'zahtev_podaci',
        ],
        'obradjen' => [
            'name' => 'obradjen',
            'label' => 'Status zahteva',
            'type' => 'closure',
        ],
        'ipaddress' => [
            'name' => 'ipaddress',
            'label' => 'IP adresa'
        ],
        'datumprijema' => [
            'name' => 'datumprijema',
            'label' => 'Datum prijema',
            'type' => 'datetime',
            'format' => 'DD.MM.YYYY. HH:mm:ss'
        ],
        'datumobrade' => [
            'name' => 'datumobrade',
            'label' => 'Datum obrade',
            'type' => 'datetime',
            'format' => 'DD.MM.YYYY. HH:mm:ss'
        ],
        'napomena',
        'created_at' => [
            'name' => 'created_at',
            'label' => 'Kreiran',
            'type' => 'datetime',
            'format' => 'DD.MM.YYYY. HH:mm:ss'
        ],
        'updated_at' => [
            'name' => 'updated_at',
            'label' => 'Ažuriran',
            'type' => 'datetime',
            'format' => 'DD.MM.YYYY. HH:mm:ss'
        ],
    ],
        // todo: privremeno, dok regionalci pozivaju telefonom ljude koji nemaju email
        $remove_columns_list_definition_array = [
//        'id',
        'licni_podaci',
//        'osoba',
        'ime',
        'prezime',
//        'brlic',
        'adresa',
        'mesto',
        'pbroj',
        'topstina_id',
        'opstina',
//        'tel',
//        'mob',
//        'email',
        'firma_podaci',
        'nazivfirm',
        'mestofirm',
        'opstinafirm',
        'emailfirm',
        'telfirm',
        'wwwfirm',
        'zahtev_podaci',
        'ipaddress',
        'datumprijema',
        'datumobrade',
//        'obradjen',
        'mbfirm',
        'pibfirm',
        'adresafirm',
//        'napomena',
//        'created_at',
//        'updated_at'
    ],

        $fields_definition_array = [
        'id' =>
            [
                'name' => 'id',
                'attributes' => ['readonly' => 'readonly'],
            ],
        'osoba' => [
            'name' => 'licenca.osobaId',
            'type' => 'relationship',
            'label' => 'Ime prezime (jmbg)',
            'attribute' => 'ime_prezime_jmbg',
            'attributes' => ['disabled' => 'disabled'],
            'ajax' => TRUE,
        ], // virtual field,
        /* 'ime' =>
             [
                 'name' => 'ime',
                 'attributes' => ['disabled' => 'disabled'],
             ],
         'prezime' =>
             [
                 'name' => 'prezime',
                 'attributes' => ['disabled' => 'disabled'],
             ],*/
        'brlic' =>
            [
                'name' => 'brlic',
                'type' => 'relationship',
                'label' => 'Broj licence',
                'attribute' => 'osobaId.licence_array',
                'entity' => 'licenca',
                'attributes' => ['disabled' => 'disabled'],
                'ajax' => TRUE,
            ],
        'adresa' => [
            'name' => 'adresa',
            'label' => 'Adresa prebivališta'
        ],
        'mesto' => [
            'name' => 'mesto',
            'label' => 'Mesto prebivališta'
        ],
        'pbroj' => [
            'name' => 'pbroj',
            'label' => 'Poštanski broj mesta prebivališta'
        ],
//        'topstina_id',
        'opstina' => [
            'name' => 'opstina',
            'type' => 'relationship',
            'attribute' => 'ime',
//            'ajax' => TRUE,
            'label' => 'Opština',
            'placeholder' => 'Odaberite opštinu prebivališta',
            'hint' => 'Odaberite jednu od ponuđenih opcija za opštinu prebivališta.',
        ],
        'tel' => [
            'name' => 'tel',
            'label' => 'Telefon'
        ],
        'mob' => [
            'name' => 'mob',
            'label' => 'Mobilni'
        ],
        'email',
        'obradjen' => [
            'name' => 'obradjen',
            'label' => 'Status zahteva',
            'type' => 'select_from_array',
        ],
        'nazivfirm' => [
            'name' => 'nazivfirm',
            'label' => 'Naziv firme'
        ],
        'mestofirm' => [
            'name' => 'mestofirm',
            'label' => 'Mesto firme'
        ],
        'opstinafirm' => [
            'name' => 'opstinafirm',
            'label' => 'Opština firme'
        ],
        'emailfirm' => [
            'name' => 'emailfirm',
            'label' => 'Email firme'
        ],
        'telfirm' => [
            'name' => 'telfirm',
            'label' => 'Telefon firme'
        ],
        'wwwfirm' => [
            'name' => 'wwwfirm',
            'label' => 'Www'
        ],
        'ipaddress',
        'datumprijema' => [
            'name' => 'datumprijema',
            'label' => 'Datum prijema',
            'type' => 'datetime',
            'format' => 'DD.MM.YYYY. HH:mm:ss'
        ],
        'datumobrade' => [
            'name' => 'datumobrade',
            'label' => 'Datum obrade',
            'type' => 'datetime',
            'format' => 'DD.MM.YYYY. HH:mm:ss'
        ],
        'mbfirm' => [
            'name' => 'mbfirm',
            'label' => 'MB'
        ],
        'pibfirm' => [
            'name' => 'pibfirm',
            'label' => 'PIB'
        ],
        'adresafirm' => [
            'name' => 'adresafirm',
            'label' => 'Adresa firme'
        ],
        'napomena',
        'created_at' => [
            'name' => 'created_at',
            'label' => 'Kreiran',
            'type' => 'datetime',
            'format' => 'DD.MM.YYYY. HH:mm:ss',
            'attributes' => [
                'readonly' => 'readonly'
            ]
        ],
        'updated_at' => [
            'name' => 'updated_at',
            'label' => 'Ažuriran',
            'type' => 'datetime',
            'format' => 'DD.MM.YYYY. HH:mm:ss'
        ],
    ],
        $remove_fields_definition_array = [
//        'id',
//        'osoba',
        'ime',
        'prezime',
//        'brlic',
        'adresa',
        'mesto',
        'pbroj',
        'topstina_id',
        'tel',
        'mob',
//        'email',
        'nazivfirm',
        'mestofirm',
        'opstinafirm',
        'emailfirm',
        'telfirm',
        'wwwfirm',
        'ipaddress',
        'datumprijema',
        'datumobrade',
        'obradjen',
        'mbfirm',
        'pibfirm',
        'adresafirm',
//        'napomena',
        'created_at',
        'updated_at',
    ];


    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\PromenaPodataka::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/clanstvo/promenapodataka');
        CRUD::setEntityNameStrings('promena podataka', 'Promena podataka');

        if (!backpack_user()->hasRole('admin')) {
            $this->crud->denyAccess('update');
        }
//        TODO: da bi se prikazala checkbox kolona za bulk action mora u setup-u da se definisu kolone, u suprotnom nece da prikaze kolonu sa chechbox-ovima
        $this->crud->setColumns($this->columns_definition_array);

        $this->crud->setColumnDetails('osoba', [
            'searchLogic' => function ($query, $column, $searchTerm) {
                if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    $query->orWhereHas('licenca.osobaId', function ($q) use ($column, $searchTerm) {
                        $q->where('ime', 'ilike', $searchTerm[0] . '%')
                            ->where('prezime', 'ilike', $searchTerm[1] . '%');
                    });
                } else {
                    $query->orWhereHas('licenca.osobaId', function ($q) use ($column, $searchTerm) {
                        $q
                            ->where('ime', 'ilike', $searchTerm . '%')
                            ->orWhere('prezime', 'ilike', $searchTerm . '%')
                            ->orWhere('id', 'ilike', $searchTerm . '%');
//                        });
                    });
                }
            }
        ]);
        $ids = [8334, 8330, 8325, 8251, 8309, 8337, 8266, 7427, 8321, 8273, 6730, 8315, 8311, 8313, 8306, 8301, 8295, 8292, 7016, 6718, 8291, 8293, 6805, 8287, 8282, 8281, 8280, 8278, 8277, 8275, 8271, 8270, 7818, 7251, 8261, 8238, 8259, 8256, 8241, 8254, 8249, 8243, 8240, 8237, 8186, 6640, 8279, 8303, 8302, 6358, 6631, 7572, 8231, 8227, 7669, 8110, 7568, 7695, 7920, 8217, 6774, 7828, 7478, 8234, 8235, 8233, 8229, 8225, 8024, 8219, 8220, 7507, 8211, 6775, 8111, 8023, 8192, 8210, 8205, 7940, 8152, 8196, 8197, 8184, 7619, 8194, 7980, 8189, 8183, 8158, 8175, 6546, 7925, 8141, 7624, 8166, 7635, 8160, 7606, 8153, 8144, 8140, 8139, 8123, 6594, 8121, 8058, 8117, 8113, 8107, 8099, 7736, 8106, 8098, 8101, 8089, 8096, 7476, 8094, 8092, 8081, 8065, 8071, 8021, 8066, 8061, 8057, 6740, 7944, 8025, 8048, 8045, 7976, 8046, 6484, 7931, 7132, 8000, 8002, 7592, 6691, 7596, 7962, 6743, 7891, 7967, 7910, 7994, 7981, 7997, 7424, 7985, 7971, 7975, 7114, 7966, 7329, 6601, 6588, 6248, 7710, 7649, 7549, 7732, 7894, 7945, 7621, 6954, 7943, 7939, 7602, 8335, 6700, 8340, 8075, 8327, 8326, 8142, 8347, 8348, 8349, 8354, 8318, 8356, 8360, 7603, 6778, 7927, 6488, 7922, 7921, 7875, 7911, 7901, 6624, 7556, 7913, 7905, 7902, 7883, 7903, 7899, 7247, 7426, 7618, 7675, 7889, 7880, 7847, 7573, 6833, 7787, 7865, 7794, 7856, 7855, 7827, 7860, 7848, 7849, 7845, 7842, 7839, 7544, 7155, 7591, 7183, 7821, 7820, 7817, 6402, 7796, 7815, 7814, 7812, 7807, 7804, 7765, 7330, 7791, 7782, 7769, 7412, 7741, 7761, 7612, 7758, 7757, 7747, 7744, 7707, 7740, 7731, 7723, 6340, 7727, 7407, 7704, 7735, 7724, 7722, 7685, 6727, 7712, 7725, 7706, 7718, 7719, 7715, 7699, 7392, 7462, 7338, 7379, 7665, 7688, 7691, 7680, 7620, 7676, 6616, 7664, 7404, 6638, 7653, 7609, 7651, 7650, 7638, 6495, 7645, 7641, 7643, 7639, 7225, 7636, 7567, 7617, 6922, 7076, 7584, 6795, 6623, 7608, 7503, 6764, 7604, 7601, 7599, 7057, 6783, 7589, 7583, 6704, 7581, 7554, 7577, 7578, 7570, 7569, 7541, 6523, 6453, 6423, 7559, 7534, 7543, 6373, 7535, 7536, 7523, 7532, 7525, 7516, 7519, 7512, 7468, 7508, 7499, 7494, 7510, 7504, 7506, 7442, 7498, 7493, 7495, 7397, 6753, 7486, 7481, 7484, 7479, 7476, 7470, 6799, 6803, 6809, 6808, 6807, 6659, 6816, 6812, 6815, 6831, 6823, 6834, 6692, 6845, 6857, 6850, 6854, 6835, 6847, 6868, 6853, 6875, 6866, 6880, 6814, 6819, 6548, 6871, 6888, 6300, 6891, 6905, 6279, 6849, 6861, 6897, 6886, 6894, 6902, 6918, 6915, 6919, 6928, 6926, 6820, 6934, 6933, 6925, 6910, 6923, 6695, 6481, 6941, 6945, 6959, 6475, 6956, 6952, 6920, 6951, 6824, 6994, 6974, 6981, 6967, 6408, 7010, 6296, 6935, 7003, 6936, 7009, 7022, 7023, 7012, 6916, 6864, 6977, 7038, 7050, 6982, 6726, 6962, 6445, 7043, 7045, 7024, 7047, 7040, 6957, 7052, 6882, 6946, 6900, 7053, 7065, 7090, 7067, 7069, 7083, 7092, 7062, 7096, 7084, 7051, 6378, 7082, 7106, 7091, 7112, 7026, 7029, 6896, 7106, 7063, 7097, 6491, 7111, 7101, 6906, 7133, 7079, 6939, 7149, 7172, 7174, 6738, 7173, 7110, 7172, 7068, 7128, 7193, 7025, 7170, 7187, 7210, 7074, 7194, 7164, 7131, 7186, 7215, 6513, 7231, 7121, 7223, 7214, 6972, 7220, 7229, 7123, 7129, 7228, 6409, 7245, 7256, 7250, 7139, 7252, 7284, 6979, 6960, 7278, 6647, 7286, 7273, 7282, 7290, 7266, 6839, 7222, 7203, 7289, 7305, 7300, 7309, 7239, 7104, 7058, 7306, 7311, 7336, 7232, 7312, 7323, 7095, 7319, 7333, 7341, 7339, 7343, 7071, 7325, 7332, 7362, 7358, 7357, 7363, 7359, 7335, 7380, 7383, 7377, 7378, 7367, 7373, 7395, 7393, 7360, 7384, 7369, 7353, 7301, 6742, 7382, 7354, 7413, 7419, 6660, 7180, 7430, 7431, 7432, 7308, 7417, 7275, 7449, 7448, 7456, 7099, 7425, 7460, 6792, 6869, 7310, 6696, 6483, 7458, 7434, 6785, 6765, 6755, 6745, 6733, 6735, 6528, 6732, 6719, 6711, 6709, 6693, 6698, 6685, 6688, 6689, 6671, 6674, 6672, 6655, 6648, 6580, 6618, 6628, 6620, 6617, 6610, 6597, 6592, 6593, 6575, 6582, 6577, 6579, 6578, 6576, 6565, 6563, 6561, 6544, 6529, 6518, 6512, 6426, 6522, 6515, 6499, 6354, 6424, 6482, 6490, 6314, 6497, 6489, 6493, 6494, 6486, 6463, 6411, 6449, 6357, 6467, 6436, 6434, 6443, 6413, 6421, 6427, 6320, 6323, 6369, 6383, 6418, 6353, 6412, 6404, 6406, 6363, 6375, 6386, 6348, 6377, 6356, 6320, 6318, 6390, 6366, 6334, 6349, 6337, 6321, 6307, 6336, 6308, 6316, 6299, 6294, 6286, 6291, 8361, 8362, 8364, 8290, 8366, 8367, 8368, 8371, 8373, 8376, 8379, 8374, 8380, 8381, 8255, 8385, 8390, 8392, 7655, 6327, 8395, 8317, 8378, 8257, 8283, 7444, 8398, 8397, 8399, 8400, 8388, 8402, 8404, 8408, 7605, 8412, 8416, 8415, 8417, 7870, 7686, 8420, 6662, 8422, 8268, 8377, 8421, 7142, 7285, 8427, 8428, 8429, 8430, 8431, 8434, 8435, 8143, 8426, 6540, 8437, 8433, 8432, 8440, 8442, 8443, 8419, 8423, 8446, 8439, 8451, 8448, 8452, 8436, 8453, 8454, 8414, 8455, 8459, 8462, 8466, 8465, 8468, 8470, 8472, 7054, 8475, 8476, 6784, 8477, 8484, 6606, 8485, 8487, 8494, 8483, 8496, 7331, 8500, 9450, 9451, 9452, 9453, 9454, 9455, 6470, 9458, 9459, 9462, 9460, 7442, 9465, 9468, 9466, 9470, 9456, 9471, 9473, 9476, 9478, 9480, 9481, 8480, 9482, 8479, 8454, 9485, 9483, 9488, 9489, 9483, 9494, 8128, 9500, 9503, 9504, 9506, 9507, 9508, 9511, 9515, 9513, 9516, 9517, 9518, 9520, 9526, 9530, 9531, 9528, 9532, 9535, 9533, 9536, 9537, 6901, 9544, 9541, 9546, 9549, 9554, 9557, 9558, 9545, 9553, 9561, 9562];

        $this->crud->addClause('whereIn', 'id', $ids);
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->removeColumns($this->remove_columns_list_definition_array);
        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */

//        todo: testirati da li radi pretraga sa licencom sa kojom nije podneo zahtev
        $this->crud->setColumnDetails('brlic', [
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('licenca', function ($q) use ($column, $searchTerm) {
                    $q->where('id', 'ilike', $searchTerm . '%');
                });
            }
        ]);

        $this->crud->setColumnDetails('obradjen', [
            'name' => 'obradjen',
            'function' => function ($entry) {
                switch ($entry->obradjen) {
                    case 0:
                        return 'Neobrađen';
                    case 1:
                        return 'Obrađen';
                    case 2:
                        return 'Duplikat';
                    case 3:
                        return 'Email';
                    case 4:
                        return 'Otkazan';
                    case 5:
                        return 'Noviji';
                    case 6:
                        return 'Potpis';
                    case 7:
                    case 16:
                    case 32:
                    case 33:
                    case 34:
                    case 35:
                    case 36:
                    case 37:
                    case 38:
                    case 39:
                    case 40:
                    case 41:
                    case 42:
                        return 'Email-neobrađen';
                    case 102:
                    case 116:
                    case 132:
                    case 133:
                    case 134:
                    case 135:
                    case 136:
                    case 137:
                    case 138:
                    case 139:
                    case 140:
                    case 141:
                    case 142:
                        return 'Email-obrađen';
                    case 202:
                    case 216:
                    case 232:
                    case 233:
                    case 234:
                    case 235:
                    case 236:
                    case 237:
                    case 238:
                    case 239:
                    case 240:
                    case 241:
                    case 242:
                        return 'Email-Problem';
                    case 300:
                        return 'Bulk-neobradjen';
                }
            },
            'wrapper' => [
                'class' => function ($crud, $column, $entry, $related_key) {
                    switch ($entry->obradjen) {
                        case 0:
                        case backpack_user()->id:
                            return 'bg-warning px-2 rounded';
                        case 1:
                            return 'bg-success text-white px-2 rounded';
                        case backpack_user()->id + 100:
                            return 'bg-info text-white px-2 rounded';
                        case backpack_user()->id + 200:
                            return 'bg-danger text-white px-2 rounded';
                    }
                }
            ]
        ]);

        /*
         * Define filters
         * start
         */
        if (!backpack_user()->hasRole('admin')) {
            $this->crud->addFilter([
                'type' => 'simple',
                'name' => 'active',
                'label' => backpack_user()->name
            ],
                FALSE,
                function () { // if the filter is active
                    $this->crud->query->whereIn('obradjen', [backpack_user()->id, backpack_user()->id + 100, backpack_user()->id + 200]); // apply the "active" eloquent scope
                });
        }

        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'neobradjeni',
            'label' => 'Neobrađeni zahtevi'
        ],
            FALSE,
            function () { // if the filter is active
//                $this->crud->query->where('obradjen', backpack_user()->id); // apply the "active" eloquent scope
                $this->crud->query->where('obradjen', NEAKTIVAN); // apply the "active" eloquent scope
            });

        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'obradjeni',
            'label' => 'Obrađeni zahtevi'
        ],
            FALSE,
            function () { // if the filter is active
//                $this->crud->query->where('obradjen', backpack_user()->id + 100); // apply the "active" eloquent scope
                $this->crud->query->where('obradjen', AKTIVAN); // apply the "active" eloquent scope
            });

/*        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'problematicni',
            'label' => 'Problematični'
        ],
            FALSE,
            function () { // if the filter is active
                $this->crud->query->where('obradjen', backpack_user()->id + 200); // apply the "active" eloquent scope
            });*/

        if (backpack_user()->hasRole('admin')) {
            $this->crud->addFilter([
                'name' => 'obradjen',
                'type' => 'select2_multiple',
                'label' => 'Status',
                'ajax' => TRUE,
            ], function () {
                return [
                    0 => '0 - Neobrađen',
                    1 => '1 - Obrađen',
                    2 => '2 - Duplikat',
                    3 => '3 - Email',
                    4 => '4 - Otkazan',
                    5 => '5 - Noviji',
                    6 => '6 - Potpis',
                    7 => '7 - Email-neobrađen',
                    16 => '16 - Email-neobrađen',
                    32 => '32 - Email-neobrađen',
                    33 => '33 - Email-neobrađen',
                    34 => '34 - Email-neobrađen',
                    35 => '35 - Email-neobrađen',
                    36 => '36 - Email-neobrađen',
                    37 => '37 - Email-neobrađen',
                    38 => '38 - Email-neobrađen',
                    39 => '39 - Email-neobrađen',
                    40 => '40 - Email-neobrađen',
                    41 => '41 - Email-neobrađen',
                    42 => '42 - Email-neobrađen',
                    116 => '116 - Email-obrađen',
                    132 => '132 - Email-obrađen',
                    133 => '133 - Email-obrađen',
                    134 => '134 - Email-obrađen',
                    135 => '135 - Email-obrađen',
                    136 => '136 - Email-obrađen',
                    137 => '137 - Email-obrađen',
                    138 => '138 - Email-obrađen',
                    139 => '139 - Email-obrađen',
                    140 => '140 - Email-obrađen',
                    141 => '141 - Email-obrađen',
                    142 => '142 - Email-obrađen',
                    216 => '216 - Email-Problem',
                    232 => '232 - Email-Problem',
                    233 => '233 - Email-Problem',
                    234 => '234 - Email-Problem',
                    235 => '235 - Email-Problem',
                    236 => '236 - Email-Problem',
                    237 => '237 - Email-Problem',
                    238 => '238 - Email-Problem',
                    239 => '239 - Email-Problem',
                    240 => '240 - Email-Problem',
                    241 => '241 - Email-Problem',
                    242 => '242 - Email-Problem',
                    300 => '300 - Bulk-neobradjen',
                ];
            }, function ($values) { // if the filter is active
                $this->crud->addClause('whereIn', 'obradjen', json_decode($values));
            });
        }

        // daterange filter
        $this->crud->addFilter([
            'type' => 'date_range',
            'name' => 'created_at',
            'label' => 'Period'
        ],
            FALSE,
            function ($value) { // if the filter is active, apply these constraints
                $dates = json_decode($value);
                $this->crud->addClause('where', 'created_at', '>=', $dates->from);
                $this->crud->addClause('where', 'created_at', '<=', $dates->to);
            });

        /*
        * end
        * Define filters
        */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(PromenaPodatakaEmailRequest::class);
        $this->crud->addFields($this->fields_definition_array);
        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();

        $this->crud->modifyField('obradjen', [
            'options' => [
                0 => '(0) Neobrađen',
                1 => '(1) Obrađen',
                2 => '(2) Duplikat',
                3 => '(3) Email',
                4 => '(4) Otkazan',
                5 => '(5) Noviji',
                6 => '(6) Potpis',
                7 => '(7) Email-neobrađen',
                16 => '(16) Email-neobrađen Tijana',
                32 => '(32) Email-neobrađen Nada',
                33 => '(33) Email-neobrađen Ljilja',
                34 => '(34) Email-neobrađen Miljan',
                35 => '(35) Email-neobrađen Jasmina',
                36 => '(36) Email-neobrađen Milorad',
                37 => '(37) Email-neobrađen Milena',
                38 => '(38) Email-neobrađen Mirjana',
                39 => '(39) Email-neobrađen Aca',
                40 => '(40) Email-neobrađen Biserka',
                41 => '(41) Email-neobrađen Edisa',
                42 => '(42) Email-neobrađen Aleksandra',
                116 => '(116) Email-obrađen Tijana',
                132 => '(132) Email-obrađen Nada',
                133 => '(133) Email-obrađen Ljilja',
                134 => '(134) Email-obrađen Miljan',
                135 => '(135) Email-obrađen Jasmina',
                136 => '(136) Email-obrađen Milorad',
                137 => '(137) Email-obrađen Milena',
                138 => '(138) Email-obrađen Mirjana',
                139 => '(139) Email-obrađen Aca',
                140 => '(140) Email-obrađen Biserka',
                141 => '(141) Email-obrađen Edisa',
                142 => '(142) Email-obrađen Aleksandra',
                216 => '(216) Email-Problem Tijana',
                232 => '(232) Email-Problem Nada',
                233 => '(233) Email-Problem Ljilja',
                234 => '(234) Email-Problem Miljan',
                235 => '(235) Email-Problem Jasmina',
                236 => '(236) Email-Problem Milorad',
                237 => '(237) Email-Problem Milena',
                238 => '(238) Email-Problem Mirjana',
                239 => '(239) Email-Problem Aca',
                240 => '(240) Email-Problem Biserka',
                241 => '(241) Email-Problem Edisa',
                242 => '(242) Email-Problem Aleksandra',
                300 => '(300) Bulk-neobradjen',
            ]
        ]);
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupShowOperation()
    {
        $this->crud->setColumns($this->columns_definition_array);

        /*
         * virtual columns
         * for separating purpose
         * start
         */
        $this->crud->modifyColumn('licni_podaci', [
            'type' => 'custom_html',
            'value' => '<div id="lpseparator"></div>
                        <script>
                            var row = document.getElementById("lpseparator").parentNode.parentNode.parentNode;
                            row.style.cssText = "background-color: rgba(124,105,239,0.2)";
                        </script>
                        '
        ]);

        $this->crud->modifyColumn('firma_podaci', [
            'type' => 'custom_html',
            'value' => '<div id="fpseparator"></div>
                        <script>
                            var row = document.getElementById("fpseparator").parentNode.parentNode.parentNode;
                            row.style.cssText = "background-color: rgba(124,105,239,0.2)";
                        </script>
                        '
        ]);

        $this->crud->modifyColumn('zahtev_podaci', [
            'type' => 'custom_html',
            'value' => '<div id="zpseparator"></div>
                        <script>
                            var row = document.getElementById("zpseparator").parentNode.parentNode.parentNode;
                            row.style.cssText = "background-color: rgba(124,105,239,0.2)";
                        </script>
                        '
        ]);
        /*
         * end
         * virtual columns
         * for separating purpose
         */

        $this->crud->modifyColumn('brlic', [
            'name' => 'brlic',
            'label' => 'Licence',
            'type' => 'select',
            'entity' => 'licenca.osobaId',
            'attribute' => 'licence_array',
            'model' => 'App\Models\Licenca',
        ]);

        $this->crud->setColumnDetails('obradjen', [
            'name' => 'obradjen',
            'function' => function ($entry) {
                switch ($entry->obradjen) {
                    case 0:
                        return 'Neobrađen';
                    case 1:
                        return 'Obrađen';
                    case 2:
                        return 'Duplikat';
                    case 3:
                        return 'Email';
                    case 4:
                        return 'Otkazan';
                    case 5:
                        return 'Noviji';
                    case 6:
                        return 'Potpis';
                    case 7:
                    case 16:
                    case 32:
                    case 33:
                    case 34:
                    case 35:
                    case 36:
                    case 37:
                    case 38:
                    case 39:
                    case 40:
                    case 41:
                    case 42:
                        return 'Email-neobrađen';
                    case 102:
                    case 116:
                    case 132:
                    case 133:
                    case 134:
                    case 135:
                    case 136:
                    case 137:
                    case 138:
                    case 139:
                    case 140:
                    case 141:
                    case 142:
                        return 'Email-obrađen';
                    case 202:
                    case 216:
                    case 232:
                    case 233:
                    case 234:
                    case 235:
                    case 236:
                    case 237:
                    case 238:
                    case 239:
                    case 240:
                    case 241:
                    case 242:
                        return 'Email-Problem';
                }
            },
            'wrapper' => [
                'class' => function ($crud, $column, $entry, $related_key) {
                    switch ($entry->obradjen) {
                        case 0:
                        case backpack_user()->id:
                            return 'bg-warning px-2 rounded';
                        case 1:
                            return 'bg-success text-white px-2 rounded';
                        case backpack_user()->id + 100:
                            return 'bg-info text-white px-2 rounded';
                        case backpack_user()->id + 200:
                            return 'bg-danger text-white px-2 rounded';
                    }
                }
            ]
        ]);

    }
}
