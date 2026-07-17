<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class CouponController extends BaseController
{
    protected $couponModel;

    public function __construct()
    {
        $this->couponModel = model('CouponModel');
    }

    public function index()
    {
        $coupons = $this->couponModel->orderBy('created_at', 'DESC')->findAll();

        return view('admin/coupons/index', [
            'title' => 'Cupons',
            'coupons' => $coupons,
        ]);
    }

    public function create()
    {
        return view('admin/coupons/form', [
            'title' => 'Novo Cupom',
            'coupon' => null,
        ]);
    }

    public function store()
    {
        $rules = [
            'code' => 'required|min_length[3]|max_length[50]|is_unique[coupons.code]',
            'type' => 'required|in_list[percentage,fixed,free_shipping]',
            'value' => 'required|decimal',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();
        $data['code'] = strtoupper($data['code']);

        // Garantir valor padrao para applies_to
        if (empty($data['applies_to'])) {
            $data['applies_to'] = 'all';
        }

        // Pula validacao do modelo pois ja validamos acima
        $this->couponModel->skipValidation()->insert($data);

        return redirect()->to('/admin/cupons')->with('success', 'Cupom criado com sucesso!');
    }

    public function edit($id)
    {
        $coupon = $this->couponModel->find($id);

        if (!$coupon) {
            return redirect()->to('/admin/cupons')->with('error', 'Cupom nao encontrado.');
        }

        return view('admin/coupons/form', [
            'title' => 'Editar Cupom',
            'coupon' => $coupon,
        ]);
    }

    public function update($id)
    {
        $rules = [
            'code' => "required|min_length[3]|max_length[50]|is_unique[coupons.code,id,{$id}]",
            'type' => 'required|in_list[percentage,fixed,free_shipping]',
            'value' => 'required|decimal',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();
        $data['code'] = strtoupper($data['code']);

        // Pula validacao do modelo pois ja validamos acima
        $result = $this->couponModel->skipValidation()->update($id, $data);

        if (!$result) {
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar cupom');
        }

        return redirect()->to('/admin/cupons')->with('success', 'Cupom atualizado!');
    }

    public function delete($id)
    {
        $this->couponModel->delete($id);

        return redirect()->to('/admin/cupons')->with('success', 'Cupom excluido!');
    }
}
