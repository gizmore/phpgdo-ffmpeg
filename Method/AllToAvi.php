<?php

namespace GDO\FFMpeg\Method;

use GDO\Core\GDT;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;

class AllToAvi extends \GDO\Form\MethodForm
{

    protected function createForm(GDT_Form $form): void
    {
        $form->actions()->addFields(GDT_Submit::make());
    }

    public function formValidated(GDT_Form $form): GDT
    {

    }

}