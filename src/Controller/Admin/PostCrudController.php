<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use DateTime;
use Doctrine\DBAL\Types\DateImmutableType;
use Doctrine\ORM\Mapping\Builder\AssociationBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use PhpParser\Node\Expr\Yield_;
use PhpParser\Node\Expr\YieldFrom;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\Validator\Constraints\Date;

class PostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    public function configureCrud(Crud  $crud) : Crud
    {
        return $crud
            ->setEntityLabelInSingular('publication')
            ->setEntityLabelInPlural('Les publications')
            ->setPageTitle(Crud::PAGE_NEW , "Ajouter une publication")
            ->setPageTitle(Crud::PAGE_EDIT , "Modifier une publication");
    }
    
    public function configureFields(string $pageName): iterable
    { 
      /**   return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
        */
        yield AssociationField::new ('tags');
        yield TextField::new ('title',"titre");
        yield TextField::new ('slug', "Lien");
        yield TextField::new ('summary',"Résumé");
        yield DateTimeField::new('PublishedAt','publier le');
        yield TextareaField::new ('content', "Contenu")->hideOnIndex();

        
    }
    
}
