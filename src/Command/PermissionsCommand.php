<?php

namespace App\Command;


use App\Entity\Permission;
use App\Entity\PermissionGroup;
use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class PermissionsCommand extends Command
{

    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * PermissionsCommand constructor.
     * @param null|string $name
     * @param EntityManagerInterface $em
     */
    public function __construct(?string $name = null, EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct($name);
    }


    protected function configure()
    {
        $this->setName('permissions:update');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return $this->permissionsUpdate($output);
    }

    private function permissionsUpdate($output)
    {
        $em = $this->em;
        $permissionsRepo = $em->getRepository('App:Permission');
        $rolesRepo = $em->getRepository('App:Role');

        ///---------------------------------------------------------------------------------
        /// -------------------------    ROLES ---------------------------------
        ///---------------------------------------------------------------------------------
        foreach (PermissionsDefinition::ROLES as $role) {
            if (!$rolesRepo->findOneBy(['name' => $role['name']])) {
                $roleEntity = new Role();
                $roleEntity->setName($role['name']);
                $roleEntity->setTitle($role['title']);
                $roleEntity->setAssignable($role['assignable']);
                $em->persist($roleEntity);
                $em->flush();
            }
        }

        ///---------------------------------------------------------------------------------
        /// -------------------------    PERMISSIONS ---------------------------------
        ///---------------------------------------------------------------------------------
        foreach (PermissionsDefinition::PERMISSIONS as $permission) {
            if (!$permissionsRepo->findOneBy([ 'name' => $permission['name']])) {
                $permissionEntity = new Permission();
                $permissionEntity->setName($permission['name']);
                $em->persist($permissionEntity);
                $em->flush();
            }
        }

        ///---------------------------------------------------------------------------------
        /// -------------------------    ROLE PERMISSION ---------------------------------
        ///---------------------------------------------------------------------------------
        foreach (PermissionsDefinition::ROLE_PERMISSIONS as $rolePermissions){
            /**
             * @var $role Role
             */
            $role = $rolesRepo->findOneBy(["name"=>$rolePermissions["role"]]);
            if(!$role) {
                $output->writeln("Role ${$rolePermissions["role"]} not found");
                continue;
            }
            foreach ($rolePermissions['permissions'] as $permissionCode){
                $permission=$permissionsRepo->findOneBy(["name"=>$permissionCode]);
                if(!$permission) {
                    $output->writeln("Permission $permissionCode not found");
                    continue;
                }
                $role->addPermission($permission);
            }
            $this->em->persist($role);
        }
        $this->em->flush();

        ///---------------------------------------------------------------------------------
        /// -------------------------    PERMISSION GROUPS ---------------------------------
        ///---------------------------------------------------------------------------------

        $permissionGroupRepo = $em->getRepository(PermissionGroup::class);

        foreach (PermissionsDefinition::PERMISSION_GROUPS as $permissionGroup) {
            /**
             * @var $oldPermissionGroup PermissionGroup
             */
            $permissionGroupEntity = $permissionGroupRepo->findOneBy(['code' => $permissionGroup['code']]);
            if (!$permissionGroupEntity) {
                $permissionGroupEntity = new PermissionGroup();
                $permissionGroupEntity->setId(md5($permissionGroup['code']));
            }


            $permissionGroupEntity->setName($permissionGroup['name']);
            $permissionGroupEntity->setCode($permissionGroup['code']);
            $permissionGroupEntity->setDescription($permissionGroup['description']);

            $permissionsArray = $permissionGroupEntity->getPermissionsArray();

            foreach ($permissionGroup['permissions'] as $permissionName) {
                $permissionEntity = $permissionsRepo->findOneBy(['name' => $permissionName]);
                if (!$permissionEntity)
                    throw new \Exception("El permiso $permissionName no ha sido definido");
                $permissionGroupEntity->addPermission($permissionEntity);


                $this->deleteElement($permissionName, $permissionsArray);
            }

            foreach ($permissionsArray as $permissionName) {
                $permissionEntity = $permissionsRepo->findOneBy(['name' => $permissionName]);
                if (!$permissionEntity)
                    throw new \Exception("El permiso $permissionName no ha sido definido");
                $permissionGroupEntity->removePermission($permissionEntity);
            }

            $em->persist($permissionGroupEntity);
        }
        $em->flush();


        $output->writeln('Permisos y roles actualizados');;
        return 0;
    }

    /**
     * Remove an element from an array.
     *
     * @param string|int $element
     * @param array $array
     */
    private function deleteElement($element, &$array)
    {
        $index = array_search($element, $array);
        if ($index !== false) {
            unset($array[$index]);
        }
    }

}
