<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\User;
use App\Services\AuthService;
use Closure;
use PDO;
use PDOException;
use RuntimeException;
use Throwable;

final class AdminUserController extends Controller
{
    private const ROLES = ['reader','author','editor'];
    private const STATUSES = ['active','locked'];
    public function __construct(private readonly Closure $pdoFactory, private readonly AuthService $auth, \App\Core\View $view){parent::__construct($view);}

    public function dashboard(): void
    {
        $admin = $this->auth->requireRole(['admin']);
        $counts=['users'=>0,'posts'=>0,'pending'=>0,'locked'=>0];$recentUsers=[];$databaseError='';
        try {$data=(new User($this->pdo()))->adminDashboard();$counts=$data['counts'];$recentUsers=$data['recent'];}
        catch(PDOException){$databaseError='Không thể tải số liệu. Hãy kiểm tra kết nối MySQL.';}
        // View chạy trước khi layout nạp includes/auth.php, nên tên admin phải
        // được truyền vào thay vì gọi currentUser() trong template.
        $adminName = (string) ($admin['full_name'] ?? 'ADMIN');
        $this->render('admin.dashboard',compact('counts','recentUsers','databaseError','adminName')+['pageTitle'=>'Tổng quan hệ thống','adminPage'=>'dashboard'],'layouts.admin');
    }

    public function users(Request $request): void
    {
        $admin=$this->auth->requireRole(['admin']);$model=new User($this->pdo());
        if($request->isPost()){
            verifyCsrf();$id=(int)$request->input('user_id',0);$action=(string)$request->input('action','');
            try{
                if($id<=0||$id===(int)$admin['id'])throw new RuntimeException('Không thể thay đổi tài khoản Admin đang đăng nhập.');
                $target=$model->adminTarget($id);if(!$target)throw new RuntimeException('Không tìm thấy tài khoản.');
                if($target['role']==='admin')throw new RuntimeException('Không thể thay đổi một Admin khác tại màn hình này.');
                if($action==='change_role'){$role=(string)$request->input('role','');if(!in_array($role,self::ROLES,true))throw new RuntimeException('Vai trò không hợp lệ.');$model->changeRole($id,$role);$_SESSION['admin_notice']='Đã cập nhật vai trò tài khoản.';}
                elseif($action==='toggle_status'){$next=$target['status']==='active'?'locked':'active';$model->changeStatus($id,$next);$_SESSION['admin_notice']=$next==='locked'?'Đã khóa tài khoản.':'Đã mở khóa tài khoản.';}
                else throw new RuntimeException('Thao tác không hợp lệ.');
            }catch(Throwable $e){$_SESSION['admin_error']=$e instanceof RuntimeException?$e->getMessage():'Không thể cập nhật tài khoản.';}
            Response::redirect(BASE_URL.'admin/users.php');
        }
        $keyword=trim((string)$request->query('q',''));$roleFilter=(string)$request->query('role','');$statusFilter=(string)$request->query('status','');
        if(!in_array($roleFilter,array_merge(['admin'],self::ROLES),true))$roleFilter='';
        if(!in_array($statusFilter,self::STATUSES,true))$statusFilter='';
        $users=$model->search($keyword,$roleFilter,$statusFilter);$notice=(string)($_SESSION['admin_notice']??'');$error=(string)($_SESSION['admin_error']??'');unset($_SESSION['admin_notice'],$_SESSION['admin_error']);
        $this->render('admin.users',compact('keyword','roleFilter','statusFilter','users','notice','error')+['pageTitle'=>'Quản lý tài khoản','adminPage'=>'users'],'layouts.admin');
    }
    private function pdo():PDO{return ($this->pdoFactory)();}
}
