<?php
/**
 * Created by PhpStorm.
 * User: jleon
 * Date: 25/06/15
 * Time: 13:56
 */
namespace Modulos\SeguridadBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;


class trazaUserRepository extends EntityRepository{

    //Clientes eliminados
    public function findTrazas(array $get, $flag = false){

        $alias = "t";
        $tableObjectName = 'SeguridadBundle:trazaUser';
        $columns = array('id','accion','fechaCreacion');
        $aColumns = array();
        foreach($columns as $value) $aColumns[] = $alias .'.'. $value;
        $aColumns[0]='t.id';
        $aColumns[1]='t.accion';
        $aColumns[2]='t.fechaCreacion';

        $cb = $this->getEntityManager()
            ->getRepository($tableObjectName)
            ->createQueryBuilder($alias)
            ->select($alias.'.id',$alias.'.accion', $alias.'.fechaCreacion')
            ;

        if ( isset( $get['iDisplayStart'] ) && $get['iDisplayLength'] != '-1' ){
            $cb->setFirstResult( (int)$get['iDisplayStart'] )
                ->setMaxResults( (int)$get['iDisplayLength'] );
        }

        if ( isset( $get['iSortCol_0'] ) ){
            for ( $i=0 ; $i<intval( $get['iSortingCols'] ) ; $i++ )
            {
                if ( $get[ 'bSortable_'.intval($get['iSortCol_'.$i]) ] == "true" ){

                    $cb->orderBy($aColumns[ (int)$get['iSortCol_'.$i] ], $get['sSortDir_'.$i]);
                }

            }

        }

        if ( isset($get['sSearch']) && $get['sSearch'] != '' ){
            $aLike = array();
            for ( $i=0 ; $i<count($aColumns) ; $i++ ){
                if ( isset($get['bSearchable_'.$i]) && $get['bSearchable_'.$i] == "true" ){

                    $aLike[] = $cb->expr()->like($aColumns[$i], '\'%'. $get['sSearch'] .'%\'');

                }
            }
            if(count($aLike) > 0) $cb->andWhere(new Expr\Orx($aLike));
            else unset($aLike);
        }

        $query = $cb->getQuery();
        $rResult = $query->getArrayResult();

        /* Data set length after filtering */
        $iFilteredTotal = count($rResult);

        /* Total data set length */
        $aResultTotal = $this->getEntityManager()
            ->createQuery('SELECT COUNT('. $alias .') FROM '. $tableObjectName .' '.$alias)
            ->setMaxResults(1)
            ->getResult();
        $iTotal = $aResultTotal[0][1];


        $get['iFilteredTotal'] = count($cb
                ->select($alias.'.id')
                ->setFirstResult(null)
                ->setMaxResults(null)
                ->getQuery()->getResult());


        if ( array_key_exists('iFilteredTotal',$get) )
        {
            $iFilteredTotal = $get['iFilteredTotal'];
        } else {
            $iFilteredTotal = $iTotalRecords;
        }
        $output = array(
            "sEcho" => intval($get['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );


        foreach($rResult as $aRow)
        {
            $row = array();

            for ( $i=0 ; $i<count($columns) ; $i++ ){
                if($i==2){
                    $row[] = $aRow[$columns[$i]]->format('d/m/Y H:i:s');

                }
                else{
                    if ( $columns[$i] == "version" ){
                        /* Special output formatting for 'version' column */
                        $row[] = ($aRow[ $columns[$i] ]=="0") ? '-' : $aRow[ $columns[$i] ];
                    }elseif ( $columns[$i] != ' ' ){

                        $row[] = $aRow[ $columns[$i] ];

                    }
                }

            }
            $output['aaData'][] = $row;
        }

        return $output;


    }

} 