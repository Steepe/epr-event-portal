<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 15/11/2025
 * Time: 05:48
 */

namespace App\Modules\Admin\Controllers;

use App\Controllers\BaseController;

class SpeakerOffersController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function index($speakerId): string|\CodeIgniter\HTTP\RedirectResponse
    {
        // Fetch speaker data
        $speaker = $this->db->table('tbl_speakers')
            ->where('speaker_id', $speakerId)
            ->get()
            ->getRowArray();

        if (!$speaker) {
            return redirect()->back()->with('error', 'Speaker not found.');
        }

        // Fetch offers
        $offers = $this->db->table('tbl_speaker_offers')
            ->where('speaker_id', $speakerId)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        return module_view('Admin', 'speaker_offers/index', [
            'offers'  => $offers,
            'speaker' => $speaker,  // <-- FIXED
        ]);
    }

    public function create($speakerId): string
    {
        return module_view('Admin', 'speaker_offers/form', [
            'speaker_id' => $speakerId,
            'offer'      => null
        ]);
    }

    public function store($speakerId): \CodeIgniter\HTTP\RedirectResponse
    {
        $data = [
            'speaker_id' => $speakerId,
            'title'      => $this->request->getPost('title'),
            'summary'    => $this->request->getPost('summary'),
            'price'      => $this->request->getPost('price'),
            'cta_link'   => $this->request->getPost('cta_link'),
        ];

        $this->db->table('tbl_speaker_offers')->insert($data);

        return redirect()->to("/admin/speakers/{$speakerId}/offers")
            ->with('success', 'Offer created');
    }

    public function edit($offerId): string|\CodeIgniter\HTTP\RedirectResponse
    {
        // Fetch the offer
        $offer = $this->db->table('tbl_speaker_offers')
            ->where('id', $offerId)
            ->get()
            ->getRowArray();

        if (!$offer) {
            return redirect()->back()->with('error', 'Offer not found.');
        }

        // Fetch the speaker associated with this offer
        $speaker = $this->db->table('tbl_speakers')
            ->where('speaker_id', $offer['speaker_id'])
            ->get()
            ->getRowArray();

        if (!$speaker) {
            return redirect()->back()->with('error', 'Speaker not found.');
        }

        return module_view('Admin', 'speaker_offers/form', [
            'offer'      => $offer,
            'speaker_id' => $offer['speaker_id'],
            'speaker'    => $speaker,     // <-- IMPORTANT
            'is_edit'    => true
        ]);
    }

    public function update($offerId): \CodeIgniter\HTTP\RedirectResponse
    {
        $offer = $this->db->table('tbl_speaker_offers')
            ->where('id', $offerId)
            ->get()
            ->getRowArray();

        $data = [
            'title'    => $this->request->getPost('title'),
            'summary'  => $this->request->getPost('summary'),
            'price'    => $this->request->getPost('price'),
            'cta_link' => $this->request->getPost('cta_link'),
        ];

        $this->db->table('tbl_speaker_offers')
            ->where('id', $offerId)
            ->update($data);

        return redirect()->to("/admin/speakers/{$offer['speaker_id']}/offers")
            ->with('success', 'Offer updated');
    }

    public function delete($offerId)
    {
        $offer = $this->db->table('tbl_speaker_offers')
            ->where('id', $offerId)
            ->get()
            ->getRowArray();

        if (!$offer) {
            return redirect()->back()->with('error', 'Offer not found');
        }

        $this->db->table('tbl_speaker_offers')
            ->where('id', $offerId)
            ->delete();

        return redirect()->to("/admin/speakers/{$offer['speaker_id']}/offers")
            ->with('success', 'Offer deleted');
    }

}
