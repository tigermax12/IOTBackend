<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use App\Models\Data;

class ImportExternalData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-external-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Conectando a la base de datos externa...');

            // Obtener datos de la base de datos externa
            $externalData = DB::connection('iotnet')
                ->table('data')
                ->select('timestamp', 'topic', 'value')
                ->where('topic', 'LIKE', '%Power%')
                ->get();

            $this->info('Datos obtenidos, comenzando la importación...');

            // Insertar datos en nuestra base de datos
            foreach ($externalData as $data) {
                Data::create([
                    'timestamp' => $data->timestamp,
                    'topic'     => $data->topic,
                    'value'     => $data->value
                ]);
            }

            $this->info('Importación completada con éxito.');
        } catch (\Exception $e) {
            $this->error('Error al importar los datos: ' . $e->getMessage());
        }
    }
}
