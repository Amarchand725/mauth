namespace App\Models;
use Illuminate\Support\Facades\Input;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class {modelName} extends Model
{
    use SoftDeletes;

    protected $table = '{tableName}';
    protected $guarded = [];

    public function getValidationRules(){
        
    }
}
